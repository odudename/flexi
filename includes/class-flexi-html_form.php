<?php

/**
 * Create HTML form tags
 * PHP Form Class from ODude.com
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */
class flexi_HTML_Form
{

	private $tag;
	private $xhtml;

	public function __construct($xhtml = true)
	{
		$this->xhtml = $xhtml;
	}

	public function startForm($action = '#', $method = 'post', $id = '', $attr_ar = array())
	{
		$str = "<form action=\"$action\" method=\"$method\"";
		if (!empty($id)) {
			$str .= " id=\"$id\"";
		}
		$str .= $attr_ar ? $this->addAttributes($attr_ar) . '>' : '>';
		return $str;
	}

	private function addAttributes($attr_ar)
	{
		$str = '';
		// check minimized (boolean) attributes
		$min_atts = array(
			'checked', 'disabled', 'readonly', 'multiple',
			'required', 'autofocus', 'novalidate', 'formnovalidate',
		); // html5
		foreach ($attr_ar as $key => $val) {
			if (in_array($key, $min_atts)) {
				if (!empty($val)) {
					$str .= $this->xhtml ? " $key=\"$key\"" : " $key";
				}
			} else {
				$str .= " $key=\"$val\"";
			}
		}
		return $str;
	}

	public function addInput($type, $name, $value, $attr_ar = array())
	{
		if (isset($attr_ar['type']) && $attr_ar['type'] == 'text_array') {
			$str = "<input type=\"$type\" name=\"" . esc_attr($name) . "[]\" value=\"" . esc_attr($value) . "\"";
		} else {
			$str = "<input type=\"$type\" name=\"" . esc_attr($name) . "\" value=\"" . esc_attr($value) . "\"";
		}
		if ($attr_ar) {
			$str .= $this->addAttributes($attr_ar);
		}
		$str .= $this->xhtml ? ' />' : '>';
		return $str;
	}

	public function addTextarea($name, $rows = 4, $cols = 30, $value = '', $attr_ar = array())
	{
		$str = "<textarea name=\"" . esc_attr($name) . "\" rows=\"" . esc_attr($rows) . "\" cols=\"" . esc_attr($cols) . "\"";
		if ($attr_ar) {
			$str .= $this->addAttributes($attr_ar);
		}
		$str .= ">$value</textarea>";
		return $str;
	}

	// for attribute refers to id of associated form element
	public function addLabelFor($forID, $text, $label_class, $attr_ar = array())
	{
		$str = "<label class='fl-label " . esc_attr($label_class) . "' for=\"$forID\"";
		if ($attr_ar) {
			$str .= $this->addAttributes($attr_ar);
		}
		$str .= ">" . esc_attr($text) . "</label>";
		return $str;
	}

	// from parallel arrays for option values and text
	public function addSelectListArrays(
		$name,
		$val_list,
		$txt_list,
		$selected_value = null,
		$header = null,
		$attr_ar = array()
	) {
		$option_list = array_combine($val_list, $txt_list);
		$str = $this->addSelectList($name, $option_list, true, $selected_value, $header, $attr_ar);
		return $str;
	}

	// option values and text come from one array (can be assoc)
	// $bVal false if text serves as value (no value attr)
	public function addSelectList(
		$name,
		$option_list,
		$bVal = true,
		$selected_value = null,
		$header = null,
		$attr_ar = array()
	) {
		$str = "<select name=\"$name\"";
		if ($attr_ar) {
			$str .= $this->addAttributes($attr_ar);
		}
		$str .= ">\n";
		if (isset($header)) {
			$str .= "  <option value=\"\">$header</option>\n";
		}
		foreach ($option_list as $val => $text) {
			$str .= $bVal ? "  <option value=\"$val\"" : "  <option";
			if (isset($selected_value) && ($selected_value === $val || $selected_value === $text)) {
				$str .= $this->xhtml ? ' selected="selected"' : ' selected';
			}
			$str .= ">$text</option>\n";
		}
		$str .= "</select>";
		return $str;
	}

	public function endForm()
	{
		return "</form>";
	}

	public function startTag($tag, $attr_ar = array())
	{
		$this->tag = esc_attr($tag);
		$str = "<$tag";
		if ($attr_ar) {
			$str .= $this->addAttributes($attr_ar);
		}
		$str .= '>';
		return $str;
	}

	public function endTag($tag = '')
	{
		$str = esc_attr($tag) ? "</$tag>" : "</$this->tag>";
		$this->tag = '';
		return $str;
	}

	public function addEmptyTag($tag, $attr_ar = array())
	{
		$str = "<$tag";
		if ($attr_ar) {
			$str .= $this->addAttributes($attr_ar);
		}
		$str .= $this->xhtml ? ' />' : '>';
		return $str;
	}
}