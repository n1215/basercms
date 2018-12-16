<?php

namespace Baser\Service\Admin;

use Baser\Model\Entity\User;
use Baser\Model\Table\UsersTable;
use Cake\Http\Exception\NotFoundException;

class UserService
{
    /**
     * @var UsersTable
     */
    private $usersTable;

    public function __construct(UsersTable $usersTable)
    {
        $this->usersTable = $usersTable;
    }

    /**
     * ユーザを取得する
     * @param int $id
     * @return User|null
     */
    public function get($id)
    {
        return $this->usersTable->get($id, [
            'contain' => ['UserGroups']
        ]);
    }

    /**
     * ユーザを追加する
     * @param array $data
     * @return User|null
     */
    public function add(array $data)
    {
        $user = $this->usersTable->newEntity();
        $user = $this->usersTable->patchEntity($user, $data);
        if (!$this->usersTable->save($user)) {
            return null;
        }
        return $user;
    }

    /**
     * ユーザを更新する
     * @param int $id
     * @param array $data
     * @return User|null
     * @throws NotFoundException
     */
    public function update($id, array $data)
    {
        $user = $this->usersTable->get($id);
        if ($user === null) {
            throw new NotFoundException();
        }

        $user = $this->usersTable->patchEntity($user, $data);
        if (!$this->usersTable->save($user)) {
            return null;
        }
        return $user;
    }

    /**
     * ユーザを削除する
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $user = $this->usersTable->get($id);
        if ($user === null) {
            throw new NotFoundException();
        }

        return $this->usersTable->delete($user);
    }

    /**
     * @return User
     */
    public function getNewEntity()
    {
        return $this->usersTable->newEntity();
    }

    /**
     * @return \Cake\ORM\Query
     */
    public function listUserGroups()
    {
        return $this->usersTable->UserGroups->find('list', ['limit' => 200]);
    }
}
