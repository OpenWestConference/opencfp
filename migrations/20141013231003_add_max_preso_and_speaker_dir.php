<?php

use Phinx\Migration\AbstractMigration;

class AddMaxPresentationsAndSpeakerDirectoryFields extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('max_presentations', 'integer');
        $table->addColumn('speaker_directory', 'integer');
        $table->save();
    }
    
    /**
     * Migrate Up.
     */
    public function up()
    {
    
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
