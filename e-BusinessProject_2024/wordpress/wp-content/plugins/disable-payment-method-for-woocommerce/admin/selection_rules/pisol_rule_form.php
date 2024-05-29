<div class="row py-3 border-bottom align-items-center bg-primary">
    <div class="col-12 col-md-4">
        <strong class="h6 text-light pi-rule-type"  data-type="disable"><?php echo esc_html($this->title); ?> <span class="text-primary">*</span></strong>
        <strong class="h6 text-light pi-rule-type"  data-type="fees">Apply fees when the below conditions are satisfied <span class="text-primary">*</span></strong><?php pisol_help::tooltip('When a customer satisfies this set of conditions, then only this rule will be applied'); ?>
    </div>
    <div class="col-12 col-md-6">
        <select class="form-control" name="pi_condition_logic">
            <option value="and" <?php selected( $this->data['pi_condition_logic'], 'and' ); ?>>All the below rules should match</option>
            <option value="or" <?php selected( $this->data['pi_condition_logic'], 'or' ); ?>>Any one of the below rule should match</option>
        </select>
    </div>
    <div class="col-12 col-md-2 text-right">
        <a href="javascript:void(0);" class="btn btn-secondary btn-sm" id="pi-add-<?php echo esc_attr($this->slug); ?>-rule" data-target="#pisol-rules-container-<?php echo esc_attr($this->slug); ?>"><?php echo esc_html__('Add Condition','disable-payment-method-for-woocommerce'); ?></a>
    </div>
</div>
<?php 
echo wp_kses($this->conditionDropdownScript(),
    array('script' => array(), 
        'select'=> array(
            'name'=>array(), 
            'class' => array()
            )
        ,
        'option' => array(
            'value' => array(),
            'selected' => array()
        ),
        'optgroup' => array(
            'label' => array()
        )
    )
); ?>
<?php echo wp_kses($this->logicDropdownScript(), 
    array(
        'script' => array(),
        'select' => array(
            'class'=>array(),
            'name'=>array(),
            'id' => array(),
            'multiple'=>array(),
            'data-condition' => array(),
            'placeholder'=>array()
        ),
        'option' => array(
            'value'=>array(),
            'selected'=>array(),
        ),
    )
); ?>
<?php 
echo wp_kses($this->savedConditions($this->saved_conditions), 
    array('script' => array())
); ?>
<div id="pisol-rules-container-<?php echo esc_attr($this->slug); ?>">
<?php 
echo wp_kses($this->savedRows(),
    array(
        'div' => array(
            'class'=> array(),
            'data-count' => array(),
            'id'=>array()
        ),
        'select' => array(
            'class'=>array(),
            'name'=>array(),
            'id' => array(),
            'multiple'=>array(),
            'data-condition' => array(),
            'placeholder'=>array()
        ),
        'input' => array(
            'class'=>array(),
            'name'=>array(),
            'id' => array(),
            'multiple'=>array(),
            'data-condition' => array(),
            'placeholder'=>array(),
            'step'=>array(),
            'min'=>array(),
            'max'=>array(),
            'value'=>array(),
            'type'=>array(),
            'readonly'=>array(),
        ),
        'option' => array(
            'value'=>array(),
            'selected'=>array(),
        ),
        'optgroup' => array(
            'label' => array()
        ),
        'a' => array(
            'href'=> array(),
            'class' => array()
        ),
        'span'=> array(
            'class'=> array()
        )
    ),
    array(
        'javascript'
    )
); ?>
</div>