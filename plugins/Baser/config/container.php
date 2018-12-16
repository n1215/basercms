<?php

return [
    \Baser\Model\Table\UsersTable::class => \DI\factory(function () {
        return \Cake\ORM\TableRegistry::getTableLocator()->get('Baser.Users');
    })
];