<?php

namespace Kanboard\Model;

use PicoDb\Database;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;

/**
 * User model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class User extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'users';

    /**
     * Id used for everbody (filtering)
     *
     * @var integer
     */
    const EVERYBODY_ID = -1;

    /**
     * Return true if the user exists
     *
     * @access public
     * @param  integer    $user_id   User id
     * @return boolean
     */
    public function exists($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->exists();
    }

    /**
     * Return true if the user is active
     *
     * @access public
     * @param  integer    $user_id   User id
     * @return boolean
     */
    public function isActive($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->eq('is_active', 1)->exists();
    }

    /**
     * Get query to fetch all users
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->db->table(self::TABLE);
    }

    /**
     * Return the full name
     *
     * @param  array    $user   User properties
     * @return string
     */
    public function getFullname(array $user)
    {
        return $user['name'] ?: $user['username'];
    }

    /**
     * Return true is the given user id is administrator
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return boolean
     */
    public function isAdmin($user_id)
    {
        return $this->userSession->isAdmin() ||  // Avoid SQL query if connected
               $this->db
                    ->table(User::TABLE)
                    ->eq('id', $user_id)
                    ->eq('role', Role::APP_ADMIN)
                    ->exists();
    }

    /**
     * Get a specific user by id
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return array
     */
    public function getById($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->findOne();
    }

    /**
     * Get a specific user by the Google id
     *
     * @access public
     * @param  string  $column
     * @param  string  $id
     * @return array|boolean
     */
    public function getByExternalId($column, $id)
    {
        if (empty($id)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq($column, $id)->findOne();
    }

    /**
     * Get a specific user by the username
     *
     * @access public
     * @param  string  $username  Username
     * @return array
     */
    public function getByUsername($username)
    {
        return $this->db->table(self::TABLE)->eq('username', $username)->findOne();
    }

    /**
     * Get user_id by username
     *
     * @access public
     * @param  string  $username  Username
     * @return integer
     */
    public function getIdByUsername($username)
    {
        return $this->db->table(self::TABLE)->eq('username', $username)->findOneColumn('id');
    }

    /**
     * Get a specific user by the email address
     *
     * @access public
     * @param  string  $email  Email
     * @return array|boolean
     */
    public function getByEmail($email)
    {
        if (empty($email)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('email', $email)->findOne();
    }

    /**
     * Fetch user by using the token
     *
     * @access public
     * @param  string   $token    Token
     * @return array|boolean
     */
    public function getByToken($token)
    {
        if (empty($token)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('token', $token)->findOne();
    }

    /**
     * Get all users
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->getQuery()->asc('username')->findAll();
    }

    /**
     * Get the number of users
     *
     * @access public
     * @return integer
     */
    public function count()
    {
        return $this->db->table(self::TABLE)->count();
    }

    /**
     * List all users (key-value pairs with id/username)
     *
     * @access public
     * @param  boolean  $prepend  Prepend "All users"
     * @return array
     */
    public function getActiveUsersList($prepend = false)
    {
        $users = $this->db->table(self::TABLE)->eq('is_active', 1)->columns('id', 'username', 'name')->findAll();
        $listing = $this->prepareList($users);

        if ($prepend) {
            return array(User::EVERYBODY_ID => t('Everybody')) + $listing;
        }

        return $listing;
    }

    /**
     * Common method to prepare a user list
     *
     * @access public
     * @param  array     $users    Users list (from database)
     * @return array               Formated list
     */
    public function prepareList(array $users)
    {
        $result = array();

        foreach ($users as $user) {
            $result[$user['id']] = $this->getFullname($user);
        }

        asort($result);

        return $result;
    }

    /**
     * Prepare values before an update or a create
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        if (isset($values['password'])) {
            if (! empty($values['password'])) {
                $values['password'] = \password_hash($values['password'], PASSWORD_BCRYPT);
            } else {
                unset($values['password']);
            }
        }

        $this->helper->model->removeFields($values, array('confirmation', 'current_password'));
        $this->helper->model->resetFields($values, array('is_ldap_user', 'disable_login_form'));
        $this->helper->model->convertNullFields($values, array('gitlab_id'));
        $this->helper->model->convertIntegerFields($values, array('gitlab_id'));
    }

    /**
     * Add a new user in the database
     *
     * @access public
     * @param  array  $values  Form values
     * @return boolean|integer
     */
    public function create(array $values)
    {
        $this->prepare($values);
        return $this->persist(self::TABLE, $values);
    }

    /**
     * Modify a new user
     *
     * @access public
     * @param  array  $values  Form values
     * @return boolean
     */
    public function update(array $values)
    {
        $this->prepare($values);
        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->update($values);
        $this->userSession->refresh($values['id']);
        return $result;
    }

    /**
     * Disable a specific user
     *
     * @access public
     * @param  integer  $user_id
     * @return boolean
     */
    public function disable($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->update(array('is_active' => 0));
    }

    /**
     * Enable a specific user
     *
     * @access public
     * @param  integer  $user_id
     * @return boolean
     */
    public function enable($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->update(array('is_active' => 1));
    }

    /**
     * Remove a specific user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return boolean
     */
    public function remove($user_id)
    {
        $this->avatarFile->remove($user_id);

        return $this->db->transaction(function (Database $db) use ($user_id) {

            // All assigned tasks are now unassigned (no foreign key)
            if (! $db->table(Task::TABLE)->eq('owner_id', $user_id)->update(array('owner_id' => 0))) {
                return false;
            }

            // All assigned subtasks are now unassigned (no foreign key)
            if (! $db->table(Subtask::TABLE)->eq('user_id', $user_id)->update(array('user_id' => 0))) {
                return false;
            }

            // All comments are not assigned anymore (no foreign key)
            if (! $db->table(Comment::TABLE)->eq('user_id', $user_id)->update(array('user_id' => 0))) {
                return false;
            }

            // All private projects are removed
            $project_ids = $db->table(Project::TABLE)
                ->eq('is_private', 1)
                ->eq(ProjectUserRole::TABLE.'.user_id', $user_id)
                ->join(ProjectUserRole::TABLE, 'project_id', 'id')
                ->findAllByColumn(Project::TABLE.'.id');

            if (! empty($project_ids)) {
                $db->table(Project::TABLE)->in('id', $project_ids)->remove();
            }

            // Finally remove the user
            if (! $db->table(User::TABLE)->eq('id', $user_id)->remove()) {
                return false;
            }
        });
    }

    /**
     * Enable public access for a user
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return bool
     */
    public function enablePublicAccess($user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $user_id)
                    ->save(array('token' => Token::getToken()));
    }

    /**
     * Disable public access for a user
     *
     * @access public
     * @param  integer   $user_id    User id
     * @return bool
     */
    public function disablePublicAccess($user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $user_id)
                    ->save(array('token' => ''));
    }
}