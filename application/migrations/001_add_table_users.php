<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_users extends CI_Migration {

        public function up()
        {
                $this->dbforge->add_field(array(
                        'user_id' => array(
                                'type' => 'INT',
                                'constraint' => 7,
                                'unsigned' => TRUE,
                                'auto_increment' => TRUE,
                                'null' => FALSE
                        ),
                        'name' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                        'mobile' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                        'password' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                ));
                $this->dbforge->add_key('user_id', TRUE);
                $this->dbforge->create_table('users');
        }

        public function down()
        {
                $this->dbforge->drop_table('users');
        }
}