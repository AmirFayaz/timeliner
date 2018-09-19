<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_data extends CI_Migration {

        public function up()
        {
                $this->dbforge->add_field(array(
                        'data_id' => array(
                                'type' => 'INT',
                                'constraint' => 7,
                                'unsigned' => TRUE,
                                'auto_increment' => TRUE,
                                'null' => FALSE
                        ),                        
                        'proj_id' => array(
                                'type' => 'INT',
                                'constraint' => 7,
                                'unsigned' => TRUE,
                                'null' => FALSE
                        ),                        
                        'param_id' => array(
                                'type' => 'INT',
                                'constraint' => 7,
                                'unsigned' => TRUE,
                                'null' => FALSE
                        ),
                        'type' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '100',
                                'null' => TRUE,
                        ),
                        'time' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '100',
                                'null' => TRUE,
                        ),
                        'value' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '100',
                                'null' => TRUE,
                        ),
                ));
                $this->dbforge->add_key('data_id', TRUE);
                $this->dbforge->create_table('data');
        }

        public function down()
        {
                $this->dbforge->drop_table('data');
        }
}