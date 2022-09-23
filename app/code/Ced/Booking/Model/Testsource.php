<?php 
namespace Ced\Booking\Model;

use Magento\Customer\Api\GroupManagementInterface;

/**
 * Class CustomerGroup
 */
class Testsource 
    extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
    implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * CustomerGroup constructor.
     * @param GroupManagementInterface $groupManagement
     */
    public function __construct(
        GroupManagementInterface $groupManagement
    )
    {
        $this->groupManagement = $groupManagement;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $groups = [];

        foreach ($this->groupManagement->getLoggedInGroups() as $group) {
            $groups[$group->getId()] = $group->getCode();
        }

        return $groups;
    }

    /**
     * Options getter
     * @return array
     */
    final public function toOptionArray() 
    {
        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
        /*$this->_options[] = ['label' => __('Please select Location'), 'value' => ''];
        $this->_options[] = ['label' => __('Please1'), 'value' => '1'];
        $this->_options[] = ['label' => __('Please2'), 'value' => '2'];
        $this->_options[] = ['label' => __('Please3'), 'value' => '3'];
        return $this->_options;*/
    }

    

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}