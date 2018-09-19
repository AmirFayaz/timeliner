<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_projects extends CI_Migration {

        public function up()
        {
                $this->dbforge->add_field(array(
                        'proj_id' => array(
                                'type' => 'INT',
                                'constraint' => 7,
                                'unsigned' => TRUE,
                                'auto_increment' => TRUE,
                                'null' => FALSE
                        ),
                        'title' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                        'created_by' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                        'created_at' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => TRUE,
                        ),
                ));
                $this->dbforge->add_key('proj_id', TRUE);
                $this->dbforge->create_table('projects');
        }

        public function down()
        {
                $this->dbforge->drop_table('projects');
        }
}