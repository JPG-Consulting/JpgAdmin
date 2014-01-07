<?php
/**
 * Copyright (c) 2014 Juan Pedro Gonzalez
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     JpgAdmin
 * @author      Juan Pedro Gonzalez
 * @copyright   2014 Juan Pedro Gonzalez
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://github.com/jpg-consulting
 */
namespace JpgAdmin\ZfcUser\Mapper\Zend;

use JpgAdmin\ZfcUser\Mapper\UserInterface;
use ZfcUser\Mapper\User as UserMapper;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Paginator;

class User extends UserMapper implements UserInterface
{
    public function findAll()
    {
        $select = $this->getSelect($this->tableName);
        $select->order(array('username ASC', 'display_name ASC', 'email ASC'));
        //$resultSet = $this->select($select);

        $resultSet = new HydratingResultSet($this->getHydrator(), $this->getEntityPrototype());
        $adapter = new Paginator\Adapter\DbSelect($select, $this->getSlaveSql(), $resultSet);
        $paginator = new Paginator\Paginator($adapter);

        return $paginator;
    }
	
    /**
     * @param \ZfcUser\Entity\UserInterface $entity
     */
    public function remove($entity)
    {
        $id = $entity->getId();
        $this->delete(array('user_id' => $id));
        $this->getEventManager()->trigger('remove', $this, array('entity' => $entity));
    }
}