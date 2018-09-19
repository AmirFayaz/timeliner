<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_parameters extends CI_Migration {

        public function up()
        {
                $this->dbforge->add_field(array(
                        'param_id' => array(
                                'type' => 'INT',
                                'constraint' => 7,
                                'unsigned' => TRUE,
                                'auto_increment' => TRUE,
                                'null' => FALSE
                        ),
                        'caption' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                        'unit' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                ));
                $this->dbforge->add_key('param_id', TRUE);
                $this->dbforge->create_table('parameters');
        }

        public function down()
        {
                $this->dbforge->drop_table('parameters');
        }
}