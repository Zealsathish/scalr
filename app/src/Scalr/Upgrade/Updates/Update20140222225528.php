<?php
namespace Scalr\Upgrade\Updates;

use Scalr\Upgrade\SequenceInterface;
use Scalr\Upgrade\AbstractUpdate;

class Update20140222225528 extends AbstractUpdate implements SequenceInterface
{
    protected $uuid = '33ddaafd-06f5-4138-a1a4-26dec989fcd7';

    protected $depends = array();

    protected $description = 'Fixing typo in SCALR_INSTANCE_INDEX';

    protected $ignoreChanges = true;


    /**
     * {@inheritdoc}
     * @see Scalr\Upgrade.SequenceInterface::getNumberStages()
     */
    public function getNumberStages()
    {
        return 1;
    }

    /**
     * Checks whether the update of the stage ONE is applied.
     *
     * Verifies whether current update has already been applied to this install.
     * This ensures avoiding the duplications. Implementation of this method should give
     * the definite answer to question "has been this update applied or not?".
     *
     * @param   int  $stage  optional The stage number
     * @return  bool Returns true if the update has already been applied.
     */
    protected function isApplied1($stage)
    {
        return false;
    }

    /**
     * Validates an environment before it will try to apply the update of the stage ONE.
     *
     * Validates current environment or inspects circumstances that is expected to be in the certain state
     * before the update is applied. This method may not be overridden from AbstractUpdate class
     * which means current update is always valid.
     *
     * @param   int  $stage  optional The stage number
     * @return  bool Returns true if the environment meets the requirements.
     */
    protected function validateBefore1($stage)
    {
        return true;
    }

    /**
     * Performs upgrade literally for the stage ONE.
     *
     * Implementation of this method performs update steps needs to be taken
     * to accomplish upgrade successfully.
     *
     * If there are any error during an execution of this scenario it must
     * throw an exception.
     *
     * @param   int  $stage  optional The stage number
     * @throws  \Exception
     */
    protected function run1($stage)
    {
        $records = $this->db->Execute("
            SELECT * FROM farm_role_settings
            WHERE name IN ('dns.int_record_alias','dns.ext_record_alias')
            AND value LIKE '%SCALR_INSATNCE_INDEX%'
        ");
        while ($record = $records->FetchRow()) {
            $record['value'] = str_replace('SCALR_INSATNCE_INDEX', 'SCALR_INSTANCE_INDEX', $record['value']);

            $this->db->Execute("
                UPDATE farm_role_settings SET value=?
                WHERE farm_roleid = ? AND name = ?
            ", array(
                $record['value'], $record['farm_roleid'], $record['name']
            ));
        }
    }
}