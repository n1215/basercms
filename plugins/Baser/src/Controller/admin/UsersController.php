<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace Baser\Controller\Admin;

use Baser\Controller\AppController;
use Baser\Service\Admin\UserService;
use N1215\CakeCandle\Http\AssistedAction;

/**
 * Users Controller
 *
 * @property \Baser\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    use AssistedAction;

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        // todo PaginationをControllerから引き剥がしたい
        $this->paginate = [
            'contain' => ['UserGroups']
        ];
        $users = $this->paginate($this->Users);
        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @param UserService $userService
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($id, UserService $userService)
    {
        $user = $userService->get($id);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @param UserService $userService
     * @return \Cake\Http\Response
     */
    public function add(UserService $userService)
    {
        $user = $userService->getNewEntity();

        if ($this->request->is('post')) {
            // todo 例外を使うか結果オブジェクトを作った方が良さそうな感じがする
            $user = $userService->add($this->request->getData());
            if ($user !== null) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }

        $userGroups = $userService->listUserGroups();
        $this->set(compact('user', 'userGroups'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @param UserService $userService
     * @return void|\Cake\Http\Response
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null, UserService $userService)
    {
        $user = $userService->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $userService->update($id, $this->request->getData());
            if ($user !== null) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $userGroups = $userService->listUserGroups();

        $this->set(compact('user', 'userGroups'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @param UserService $userService
     * @return \Cake\Network\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function delete($id = null, UserService $userService)
    {
        $this->request->allowMethod(['post', 'delete']);

        if ($userService->delete($id)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
