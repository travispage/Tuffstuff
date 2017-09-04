<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */

/**
 *
 *
 * @since 1.4
 */
abstract class SiteTreeFieldView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $field;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $id;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $name;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $value;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function __construct( $field, $sectionId = '' ) {
		$this->field = $field;
		$this->name  = 'sitetree';
		
		if ( $sectionId ) {
			$this->id	 = $sectionId . '-' . $this->field->id;
			$this->name .= '[' . $sectionId . ']';
		}
		else { $this->id = $this->field->id; }
		
		$this->id	 = str_replace( '_', '-', $this->id );
		$this->name .= '[' . $this->field->id . ']';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function setValue( $value ) {
		$this->value = $value;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	abstract public function render();
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function render_tooltip() {
		if ( $this->field->tooltip ) {
			$tag = isset( $this->field->config['tooltip_pos'] ) ? 'p' : 'span';
		
			echo "\n<" . $tag . ' class="description">';
			
			echo wp_kses( $this->field->tooltip, array( 'code' => true, 'a' => array( 'href' => true ) ) );
			
			echo "</{$tag}>";
		}
	}
}


/**
 *
 *
 * @since 1.4
 */
class SiteTreeCheckbox extends SiteTreeFieldView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function render() {
		echo '<label><input type="checkbox" id="' . $this->id . '" name="' . $this->name 
		   . '" value="1"' . checked( true, $this->value, false ) . ">\n" . $this->field->tooltip . '</label>';
	}
}


/**
 *
 *
 * @since 1.4
 */
class SiteTreeDropdown extends SiteTreeFieldView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function render() {
		echo '<select id="' . $this->id . '" name="' . $this->name . '">';
		
		foreach ( $this->field->config as $value => $label )
			echo '<option value="' . $value . '"' . selected( $value, $this->value, false ) . '>' . $label . '</option>';
		
		echo '</select>';
		
		$this->render_tooltip();
	}
}

/**
 *
 *
 * @since 1.4
 */
class SiteTreeTextField extends SiteTreeFieldView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function render() {
		echo '<input type="text" id="' . $this->id . '" name="' . $this->name 
		   . '" value="' . $this->value . '" class="regular-text">';
		   
		$this->render_tooltip();
	}
}

/**
 *
 *
 * @since 1.4
 */
class SiteTreeTextarea extends SiteTreeFieldView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function render() {
		echo '<textarea name="' . $this->name . '" class="code" />' . $this->value . '</textarea><br />';
		   
		$this->render_tooltip();
	}
}

/**
 *
 *
 * @since 1.4
 */
class SiteTreeNumberField extends SiteTreeFieldView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function render() {
		echo '<input type="number" id="' . $this->id . '" name="' . $this->name . '" value="' . $this->value
		   . '" min="' . $this->field->config['min_value'] . '" max="' . $this->field->config['max_value'] . '" class="small-text">';
		   
		$this->render_tooltip();
	}
}
?>