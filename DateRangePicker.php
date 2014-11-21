<?php
namespace webvimark\extensions\DateRangePicker;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * DateRangePicker
 *
 * Wrapper for https://github.com/dangrossman/bootstrap-daterangepicker 
 * 
 * @author vi mark <webvimark@gmail.com> 
 * @license MIT
 */
class Drp extends Widget
{
        /**
         * @var string
         */
        public $selector;
        /**
         * @var string
         */
        public $model;
        /**
         * @var string
         */
        public $attribute;
        /**
         * Datepicker params 
         * 
         * @var array
         */
        public $params = array();

        public $applyCallback = '';


        protected $_selector;
	protected $_params = array(
                'opens'          => 'left',
                'format'         => 'YYYY-MM-DD H:mm',
                'startDayOfWeek' => 1,
        );


        /**
         * init 
         */
        public function run()
        {
                if ( $this->selector )
                {
                        $this->_selector = $this->selector;
                }
                elseif ( $this->model AND $this->attribute ) 
                {
                        $this->_selector = 'input[name="' . $this->model .'[' . $this->attribute . ']"]';
                } 

                if ( ! $this->_selector )
                        throw new InvalidConfigException('Define selector or model + attributes');

		DateRangePickerAsset::register($this->view);


                // If applyCallback not set, then we try to update given grid
                if ( ! $this->applyCallback AND $this->model AND $this->attribute AND $this->selector ) 
                {
                        $this->applyCallback = "$('input[name=\"{$this->model}[{$this->attribute}]\"]').val(picker.startDate.format('{$this->_params['format']}') + ' - ' + picker.endDate.format('{$this->_params['format']}'));";
                        $this->applyCallback .= "$('input[name=\"{$this->model}[{$this->attribute}]\"]').trigger('change');";
                }

		$this->view->registerJs(<<<JS

			 $(document).on('mouseup', '$this->_selector', function(){
				$(this).daterangepicker({$this->_mergeParams()});
			});
			$(document).on('apply','$this->_selector', function(ev, picker) {
				$('{$this->_selector}').trigger('change');
				{$this->applyCallback};
			});
JS
);

        }

        /**
         * _mergeParams 
         * 
         * @return string json array
         */
        protected function _mergeParams()
        {
                return json_encode(ArrayHelper::merge($this->_params, $this->params));
        }
}
