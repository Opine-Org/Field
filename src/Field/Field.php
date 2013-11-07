<?php
namespace Field;

class Field {
	private $db;
	private $captcha;
	private $fieldContainer = [];
	private $root;
	
	public function __construct ($root, $db, $captcha) {
		$this->db = $db;
		$this->captcha = $captcha;
		$this->root = $root;
	}

	public function render ($type, $metadata) {
		if (!isset($this->fieldContainer[$type])) {
			$path = $this->root . '/../fields/' . $type . '.php';
			if (!file_exists($path)) {
				$path = __DIR__ . '/../../available/' . $type . '.php';
			}
			if (!file_exists($path)) {
				throw new \Exception('Unknown field type: ' . $type);
			}
			require_once $path;
			$className = 'Field\\' . $type;
			$instance = new $className($this, $this->db);
			$instance->db = $this->db;
			$instance->fieldService = $this;
			$this->fieldContainer[$type] = $instance;
		}
		$instance = $this->fieldContainer[$type];
		if (!isset($metadata['attributes'])) {
			$metadata['attributes'] = [];
		}
		ob_start();
		$instance->render($metadata);
		return ob_get_clean();
	}

	public function isAssociative (&$array) {
		if (!is_array($array)) {
			return false;
		}
		if (array_values($array) === $array) {
			return false;
		}
		return true;
	}

	public function arrayToCsv (array $array) {
		foreach ($array as &$value) {
			if (substr_count($value, ',') > 0) {
				$value = '"' . str_replace('"', '\"', $value) . '"';
			}
		}
		return htmlentities(implode(', ', $array));
	}
	
	public function forceAssociative (&$array) {
		if (!is_array($array)) {
			return [];
		}
		$newArray = [];
		foreach ($array as $value) {
			$newArray[(string)$value] = $value;
		}
		return $newArray;
	}

	public function tag (&$field, $tag, $attributes=[], $closed=true, $data='') {
		if (isset($attributes['name']) && substr_count($attributes['name'], '-') > 0) {
			$tmp = explode('[', substr($attributes['name'], 0, -1), 2);
			$marker = $tmp[0];
			$name = $tmp[1];
			$name = explode('-', $name);
			$attributes['name'] = $marker;
			foreach ($name as $namePart) {
				$attributes['name'] .= '[' . $namePart . ']';
			}
		}
		
		echo '<', $tag, ' ';
		foreach ($attributes as $attribute => $value) {
			echo ' ', $attribute, '="', $value, '" ';
		}
		if ($closed === true) {
			echo ' />';
		} elseif ($closed === false) {
			echo '>' . $data . '</' . $tag . '>';
		} else {
			echo '>';
		}
	}

	public function admin ($attributes=[]) {
		return function ($field) use ($attributes) {
			$parentAdmin = $field['__admin'];
			$selector = '#' . $field['name'] . '-field';
			$options = ['appendJSFunction' => 'subDocumentAppended'];
			if (isset($field['data']) && is_array($field['data'])) {
				$options['documents'] = $field['data'];
				$setIds = false;
				if (is_array($options['documents']) && count($options['documents'])) {
					foreach ($options['documents'] as &$doc) {
						if (!isset($doc['_id'])) {
							$setIds = true;
							$doc['_id'] = new \MongoId();
						}
					}
					if ($setIds === true) {					
						Mongo::collection($parentAdmin->storage['collection'])->update(
							['_id' => Mongo::id($parentAdmin->activeRecord['_id'])],
							['$set' => [$field['name'] => $options['documents']]]
						);
					}
				}
			} else {
				$options['documents'] = [];
			}
			$admin = AdminModel::request($field['adminClass'], '', false, false, $options);
			$admin['admin']->displayMode = 'echo';
			$admin['admin']->action = '/admin.php';
			$admin['admin']->subDocument = true;
			echo '<div class="sub-document" data-parent="', get_class($parentAdmin), '" data-field="', $field['name'], '" data-id="', (string)$parentAdmin->activeRecord['_id'], '">';
			AdminModel::render($admin['admin'], $admin['data'], $selector);
			echo '</div>';
		};
	}

	public function addClass (Array &$attributes, $class) {
		if (isset($attributes['class'])) {
			$attributes['class'] .= ' ' . $class;
		} else {
			$attributes['class'] = $class;
		}
	}

	public function textarea ($attributes=[]) {
		return function ($field) use ($attributes) {
		};
	}

	public function nameFromId ($attributes=[]) {
		return function ($field) use ($attributes) {
			if (!isset($field['key'])) {
				$field['key'] = '_id';
			}
			$document = DB::collection($field['collection'])->findOne([$field['key'] => DB::id($field['key'])], [$field['value']]);
			if (!isset($document['_id'])) {
				echo '';
			} else {
				echo $document[$field['value']]; 
			}
			$attributes['type'] = 'hidden';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			Field::tag($field, 'input', $attributes);
		};
	}

	public function select ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';			
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
			
			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}
			
			if (isset($field['readonly']) && $field['readonly'] == true) {
				$attributes['class'] .= ' input-xlarge uneditable-input ';
				if (isset($field['data']) && !empty($field['data'])) {
					if (isset($field['options'][(string)$field['data']])) {
						Field::tag($field, 'span', $attributes, false, $field['options'][(string)$field['data']]);
						$function = self::inputHidden();
						$function($field);
						return;
					}
				}
			}
			
			Field::tag($field, 'select', $attributes, 'open');
			if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
				if ($field['nullable'] === true) {
					$field['nullable'] = '';
				}
				echo '<option value="">', $field['nullable'], '</option>';
			}
			if (is_array($field['options'])) {
				foreach ($field['options'] as $key => $value) {
					echo '<option value="', $key, '">', $value, '</option>';
				}
			}
			echo '</select>';
		};
	}
	
	public function multiSelectCheckbox ($attributes=[]) {
		//DOMView::includeFile('/vc/cl/js/jquery-ui-multiselect/jquery.multiselect.min.js', 5);
		//DOMView::includeFile('/vc/cl/js/jquery-ui-multiselect/jquery.multiselect.css', 5);
		return function ($field) use ($attributes) {
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . '][]';
			$attributes['multiple'] = 'multiple';
			$attributes['class'] = (empty($attributes['class'])) ? 'multiSelectCheckbox' : $attributes['class'] . ' multiSelectCheckbox'; 
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
			
			Field::tag($field, 'select', $attributes, 'open');
			if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
				if ($field['nullable'] === true) {
					$field['nullable'] = '';
				}
				echo '<option value="">', $field['nullable'], '</option>';
			}
			
			if (is_array($field['options'])) {
				foreach ($field['options'] as $key => $value) {
					if (!is_array($value)) {
						$selected = (!empty($field['data']) && in_array($key, $field['data'])) ? 'selected="selected"' : null;
						echo '<option value="', $key, '" ',$selected,'>', $value, '</option>';
					} else {
						echo '<optgroup label="',$value['label'],'">';
							foreach ($value['options'] as $k => $v) {
								$selected = (!empty($field['data']) && in_array($k, $field['data'])) ? 'selected="selected"' : null;
								echo '<option value="', $k, '" ',$selected,'>', $v, '</option>';
							}
						echo '</optgroup>';
					}
				}
			}
			echo '</select>
			<script type="text/javascript">
				$(function () {
	 				$(".multiSelectCheckbox").multiselect(); 
				});
			</script>';
		};
	}

    public function selectToMenu ($attributes=[]) {
        return function ($field) use ($attributes) {
            $attributes['type'] = 'hidden';
            $attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
            if (is_callable($field['options'])) {
                $function = $field['options'];
                $field['options'] = $function();
            };

            if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
                if ($field['nullable'] === true) {
                    $field['nullable'] = 'Click to Choose';
                }
            } else {
                $field['nullable'] = 'Click to Choose';
            }
            $ancestor = '';
            $label = '';
            $selected = '';
            if (!empty($field['data'])) {
                foreach ($field['options'] as $optionGroup) {
                    $ancestor = $optionGroup['label'];
                    foreach ($optionGroup['options'] as $optionKey => $optionValue) {
                        if ((string)$optionKey == (string)$field['data']) {
                            $label = $optionValue;
                            $selected = 'Selected: ' . $ancestor . ' <i class="icon-long-arrow-right icon-white"></i> ' . $label;
                            break 2;
                        }
                    }
                }
            }

            echo '
                <div class="select-to-menu btn-group" data-nullable="', $field['nullable'], '">';

            Field::tag($field, 'input', $attributes);

            echo '
                    <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        ', (($selected == '') ? $field['nullable'] : $selected), '
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu first">';

            if ($selected != '') {
                echo '<li class="init"><a data-id="" onclick="selectToMenu(this)" style="cursor: pointer">Clear Selected</a></li>';
            }

            foreach ($field['options'] as $optionGroup) {
                $wideClass = 'wide0';
                if (isset($optionGroup['options']) && is_array($optionGroup['options']) && count($optionGroup['options']) > 14) {
                    $wideClass = 'wide' . floor(count($optionGroup['options']) / 14);
                }
                echo '
                        <li class="dropdown-submenu">
                            <a tabindex="-1" style="cursor: pointer">', $optionGroup['label'], '</a>';

                if (isset($optionGroup['options']) && is_array($optionGroup['options']) && count($optionGroup['options']) > 0) {
                    echo '
                            <ul class="dropdown-menu ' . $wideClass . '">';
                    foreach ($optionGroup['options'] as $optionKey => $optionValue) {
                        echo '

                                <li><a onclick="selectToMenu(this)" style="cursor: pointer" data-id="', $optionKey, '">', $optionValue, '</a></li>';
                    }
                    echo '
                            </ul>';
                }
                echo '
                        </li>';
            }
            echo '
                    </ul>
                </div>';
        };
    }

    public function selectOptionGroup ($attributes=[]) {
        return function ($field) use ($attributes) {
            $attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
            if (is_callable($field['options'])) {
                $function = $field['options'];
                $field['options'] = $function();
            };

            Field::tag($field, 'select', $attributes, 'open');
            if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
                if ($field['nullable'] === true) {
                    $field['nullable'] = '';
                }
                echo '<option value="">', $field['nullable'], '</option>';
            }
            foreach ($field['options'] as $optionGroup) {
                echo '
                    <optgroup label="', $optionGroup['label'], '">';

                if (isset($optionGroup['options']) && is_array($optionGroup['options']) && count($optionGroup['options']) > 0) {
                    foreach ($optionGroup['options'] as $optionKey => $optionValue) {
                        echo '
                            <option value="', $optionKey, '">', $optionValue, '</option>';
                    }
                }
                echo '
                    </optgroup>';
            }
            echo '
                    </select>';
        };
    }

	public function selectToPill ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['value'] = [];
			$attributes['key'] = [];
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';				
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}
			
			if (isset($field['data']) && is_array($field['data'])) {
				$attributes['value'] = [];
				$attributes['key'] = [];
				foreach ($field['data'] as $key) {
					if (isset($field['options'][$key])) {
						$attributes['value'][] = $field['options'][$key];
						$attributes['key'][] = $key;
					}
				}
			}
				
			ob_start();
			$availableCount = 0;
			if (is_array($field['options'])) {
				foreach ($field['options'] as $key => $value) {
					$style = '';
					$used = false;
					if (!in_array($key, $attributes['key'])) {
						$availableCount++;
						$style = ' style="display: block" ';
					} else {
						$style = ' style="display: none" ';
					}
					echo '<li ', $style, ' data-id="', $key, '"><a onclick="selectToPill(this)">', $value, '</a></li>';
				}
			}
			$available = ob_get_clean();
			
			ob_start();
			$chosenCount = count($attributes['value']);
			for ($i=0; $i < $chosenCount; $i++) {
				echo '
					<li style="position: relative" data-id="' . $attributes['key'][$i] . '">
						<a style="cursor: pointer">', $attributes['value'][$i], '
							<span class="ui-icon ui-icon-closethick ui-icon-triangle-1-s"></span>
						</a>
						<input type="hidden" name="', $attributes['name'], '[]" value="', $attributes['key'][$i], '" />
					</li>';
			}
			$chosen = ob_get_clean();
			
			$title = '';
			if (isset($field['tooltip'])) {
				$title = $field['tooltip'];
			}

			//DOMView::includeFile('/vc/cl/css/fields/select-to-pill.css', 5);

			echo '
				<div title="', $title, '" class="select-to-pill" data-name="', $attributes['name'], '">
					<div class="dropdown">
						<a class="btn">', $field['label'], '<span class="caret"></span></a>
						<ul class="dropdown-menu" data-count="', $availableCount, '" style="max-height: 200px; overflow: auto">', $available, '</ul>
					</div>
					<ul class="nav nav-pills nav-stacked">', $chosen, '</ul>
				</div>';
		};
	}

	public function inputFileMulti ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'file';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
	
			echo '
			<div id="', uniqid('file__'), '" class="fileupload-container-2" data-name="', $attributes['name'], '">
				<div class="row fileupload-buttonbar">
					<div class="span7">
						<span class="btn btn-success fileinput-button">
						<i class="icon-plus icon-white"></i>
						<span>Add files...</span>
							<input type="file" name="files[]" multiple="multiple" />
						</span>
						<button type="submit" class="btn btn-primary start">
							<i class="icon-upload icon-white"></i>
							<span>Start upload</span>
						</button>
						<button type="reset" class="btn btn-warning cancel">
							<i class="icon-ban-circle icon-white"></i>
							<span>Cancel upload</span>
						</button>
						<button type="button" class="btn btn-danger delete">
							<i class="icon-trash icon-white"></i>
							<span>Delete</span>
						</button>
						<input type="checkbox" class="toggle" />
					</div>
					<div class="span5 fileupload-progress fade">
						<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
							<div class="bar" style="width:0%;"></div>
						</div>
						<div class="progress-extended">&nbsp;</div>
					</div>
				</div>
				<div class="fileupload-loading"></div>
				<br />
				<table role="presentation" class="table table-striped">
					<tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
				</table>
			</div>
			${FILEUPLOAD-TEMPLATE}';

/*
			DOMView::addString('${FILEUPLOAD-TEMPLATE}', '<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields=\'{"withCredentials":true}\'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>');
*/
		};
	}
	
	public function inputPassword ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'password';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			Field::tag($field, 'input', $attributes);
		};
	}

	public function inputFile ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'file';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			
			echo '
				<div id="', uniqid('file__'), '" class="fileupload_bs fileupload_bs-new fileupload-container" data-provides="fileupload_bs" data-name="', $attributes['name'], '">
					<div class="input-append">
						<div class="uneditable-input span2">
							<i class="icon-file fileupload_bs-exists"></i> 
							<span class="fileupload_bs-preview"></span>
						</div>
						<span class="btn btn-file">
							<span class="fileupload_bs-new">Select file</span>
							<span class="fileupload_bs-exists">Change</span>
							<input class="fileupload" type="file" name="files[]" />
						</span>
						<a href="#" class="btn fileupload_bs-exists" data-dismiss="fileupload">Remove</a>
					</div>
					<div class="progress" style="display: none">
						<div class="bar" style="width: 0%;"></div>
					</div>';
			
//			echo '
//				<div id="', uniqid('file__'), '" class="fileupload-container" data-name="', $attributes['name'], '">
//					<input class="fileupload" type="file" name="files[]" />
//					<div class="progress" style="display: none">
//						<div class="bar" style="width: 0%;"></div>
//					</div>
//			';

			if (isset($field['data']) && is_array($field['data']) && count($field['data']) > 0) {
				if (!isset($field['data']['name']) && isset($field['data']['url'])) {
					$field['data']['name'] = pathinfo($field['data']['url']);
					$field['data']['name'] = $field['data']['name']['filename'];
				}
				foreach ($field['data'] as $key => $value) {
					echo '<input type="hidden" name="', $attributes['name'], '[', $key, ']" value="', $value, '" />';
				}
				echo '<p><a class="img-preview" data-type="', $field['data']['type'], '" data-height="', $field['data']['height'], '" data-width="', $field['data']['width'], '" data-href="', $field['data']['url'], '">', $field['data']['name'], '</a></p>';
			}

			echo '
				</div>';
		};
	}

	public function inputRadio ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};

			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}
			
			if (is_array($field['options'])) {
				echo '<ul class="form-list-rdo">';
				foreach ($field['options'] as $optionKey => $option) {
					if (is_array($option)) {
						foreach ($option as $key => $value) {
							echo '<li><input type="radio" name="', $attributes['name'], '" value="', $key, '" /> <label class="form-lbl">', $value, '</label></li>';
							break;
						}
					} else {
						echo '<li><input type="radio" name="', $attributes['name'], '" value="', $optionKey, '" /> <label class="form-lbl">', $option, '</label></li>';
					}
				}
				echo '</ul>';
			}
		};
	}
	
	public function inputRadioBootstrap ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			Field::label($field);
	
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}
			
			$inline = '';
			if (isset($field['inline'])) {
				$inline = ' inline ';
			}
            if (!isset($field['tagContainerClass'])) {
                $field['tagContainerClass'] = 'controls';
            }
			if (is_array($field['options'])) {
				echo '<div class="', $field['tagContainerClass'], '">';
				foreach ($field['options'] as $optionKey => $option) {
					if (is_array($option)) {
						foreach ($option as $key => $value) {
							echo '<label class="radio ', $inline, '"><input type="radio" name="', $attributes['name'], '" value="', $key, '" /> ', $value, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>';
							break;
						}
					} else {
						echo '<label class="radio ', $inline, '"><input type="radio" name="', $attributes['name'], '" value="', $optionKey, '" /> ', $option, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>';
					}
				}
				if ($field['other'] == true) {
					echo '
					<label class="radio ', $inline, '" style="width: auto"><input id="', $field['name'], '-other" type="radio" name="', $attributes['name'], '" value="vc-other" /> Other: </label>
					<input class="vc-other" style="', $field['other-style'], '" data-id="', $field['name'], '-other" type="text" name="', str_replace(']', '-other]', $attributes['name']), '" maxlength="', $field['other-maxlength'], '" />';
				}
				echo '</div>';
			}
		};
	}

    public function recaptcha ($attributes=[]) {
        return function ($field) use ($attributes) {
            $attributes['type'] = 'hidden';
            $attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
            Field::tag($field, 'input', $attrib>utes);
            $this->captcha->setPublicKey($this-Config::captcha()['publickey']);
			$this->captcha->setPrivateKey(Config::captcha()['privatekey']);
			echo $captcha->html();
        };
    }

	public function inputRadioButton ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';

			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};

			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}

			if (is_array($field['options'])) {
				echo '<div class="radioset-make ', (($field['other'] == true) ? 'radioset-other' : ''), '">';
				foreach ($field['options'] as $optionKey => $option) {
					if (is_array($option)) {
						foreach ($option as $key => $value) {
							$id = 'radio_' . uniqid();
							echo '<input type="radio" id="', $id, '" name="', $attributes['name'], '" value="', $key, '" /><label for="', $id, '">', $value, '</label>';
							break;
						}
					} else {
						$id = 'radio_' . uniqid();
						echo '<input type="radio" id="', $id, '" name="', $attributes['name'], '" value="', $optionKey, '" /><label for="', $id, '">', $option, '</label>';
					}
				}
                if ($field['other'] == true) {
                    $id = 'radio_' . uniqid();
                    echo '
                        <input type="radio" id="', $id, '" name="', $attributes['name'], '" value="vc-other" /><label for="', $id, '">Other</label>
					    <div style="', $field['other-style'], '" class="control-group vc-other"><label class="control-label">Other Amount: </label><div class="controls"><input data-id="', $field['name'], '-other" type="text" name="', str_replace(']', '-other]', $attributes['name']), '" maxlength="', $field['other-maxlength'], '" /></div></div>';
                }
				echo '</div>';
			}
		};
	}

	public function inputCodename ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'hidden';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			if (isset($field['path'])) {
				$attributes['data-path'] = $field['path'];
				if (!isset($attributes['class'])) {
					$attributes['class'] = '';
				}
				$attributes['class'] .= ' vcms-permalink';
				$attributes['class'] = trim($attributes['class']);
			}
			if (isset($field['selector'])) {
				$attributes['data-selector'] = $field['selector'];
			}
			if (isset($field['mode'])) {
				$attributes['data-mode'] = $field['mode'];
			}
			Field::tag($field, 'input', $attributes);
		};
	}
	
	public function inputHidden ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'hidden';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			Field::tag($field, 'input', $attributes);
		};
	}

    public function inputCheckboxNaked ($attributes=[]) {
        return function ($field) use ($attributes) {
            $attributes['type'] = 'checkbox';
            $attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
            if (isset($field['options']) && is_array($field['options'])) {
                foreach ($field['options'] as $option) {
                    if (is_array($option)) {
                        foreach ($option as $key => $value) {
                            if ($key == 0) {
                                $attributes['value'] = $value;
                            } else {
                                $attributes['value'] = $key;
                            }
                            break;
                        }
                    } else {
                        $attributes['value'] = $option;
                    }
                }
            }

            if (!Field::isAssociative($field['options'])) {
                $field['options'] = Field::forceAssociative($field['options']);
            }
            Field::tag($field, 'input', $attributes);
        };
    }

	public function inputCheckbox ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'checkbox';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			$label = '';
			if (isset($field['options']) && is_array($field['options'])) {
				foreach ($field['options'] as $option) {
					if (is_array($option)) {
						foreach ($option as $key => $value) {
							if ($key == 0) {
								$attributes['value'] = $value;
								$label = $value;
							} else {
								$attributes['value'] = $key;
								$label = $value;
							}
							break;
						}
					} else {
						$attributes['value'] = $option;
						$label = $option;
					}
				}
			}
			
			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}
			
			echo '
				<ul class="form-list-chk">
					<li>';
			Field::tag($field, 'input', $attributes);
			
			echo '
						<label class="form-lbl">', $label, '</label>
					</li>
				</ul>';
		};
	}

	public function inputCheckboxes ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'checkbox';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';

			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
			
			if (is_array($field['options'])) {
				echo '<ul class="form-list-chk">';
				foreach ($field['options'] as $option) {
					if (is_array($option)) {
						foreach ($option as $key => $value) {
							echo '<li><input type="checkbox" name="', $attributes['name'], '[', $key, ']" value="on" /> <label class="form-lbl">', $value, '</label></li>';
							break;
						}
					} else {
						echo '<li><input type="checkbox" name="', $attributes['name'], '[', $option, ']" value="on" /> <label class="form-lbl">', $option, '</label></li>';
					}
				}
				echo '</ul>';
			}
		};
	}
	
	public function inputCheckboxesList ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'checkbox';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';	
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
				
			if (is_array($field['options'])) {
				foreach ($field['options'] as $option) {
					if (is_array($option)) {
						foreach ($option as $key => $value) {
							echo '
								<label class="checkbox">
									<input type="checkbox" name="', $attributes['name'], '[', $key, ']', '" />', $value, 
								'</label>';							
							break;
						}
					} else {
						echo 
							'<label class="checkbox">
								<input type="checkbox" name="', $attributes['name'], '[', $option, ']', '" value="on" />', $option,
							'</label>';
					}
				}
			}
		};
	}
	
	public function inputRadioList ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'checkbox';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
	
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
	
			if (is_array($field['options'])) {
				foreach ($field['options'] as $optionKey => $option) {
					if (is_array($option)) {
						foreach ($option as $key => $value) {
							echo '
							<label class="radio">
								<input type="radio" value="', $key, '" name="', $attributes['name'], '" />', $value,
							'</label>';
							break;
						}
					} else {
						echo
						'<label class="radio">
							<input type="radio" value="', $optionKey, '" name="', $attributes['name'], '" />', $option,
						'</label>';
					}
				}
			}
		};
	}

	public function inputDatePicker ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['class'] = 'datepicker';
			if (isset($field['datetimepicker'])) {
				Field::addClass($attributes, 'datetimepicker');
			}
			if (isset($field['timepicker'])) {
				Field::addClass($attributes, 'timepicker');
			}
			//Field::addClass($attributes, 'search-query');
			$attributes['type'] = 'text';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			$attributes['style'] = 'text-indent: 28px; line-height: 19px';
			$attributes['autocomplete'] = 'off';
			$attributes['spellcheck'] = 'false';
			$calendarLeft = 10;
			if (isset($field['calendarLeft'])) {
				$calendarLeft = $field['calendarLeft'];
			}
			
			echo '
				<div style="position: relative;">
					<i class="icon-calendar" style="position: absolute; left: ', $calendarLeft, 'px; top: 7px; opacity: .3"></i>';

					Field::tag($field, 'input', $attributes);
				
			echo '
				</div>';
			
		};
	}
	
	public function inputToTags ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['type'] = 'text';
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			$attributes['id'] = 'tags_' . uniqid();
			$attributes['class'] = 'input-to-tags';
			if (isset($field['data']) && is_array($field['data'])) {
				$attributes['value'] = htmlentities(implode(',', $field['data']));
			}
			Field::tag($field, 'input', $attributes);
			
			$autocomplete = '';
			if (isset($field['autocomplete'])) {
				$autocomplete = ',autocomplete_url: "/admin/autocomplete/?marker=' . str_replace('\\', '_', $field['__CLASS__']) . '-' . $field['name'] . '"';
			}

			echo '<script>
				$("#', $attributes['id'], '").tagsInput({
					width: "auto", height: "auto"',
					$autocomplete, '
				});
			</script>';
		};
	}

	public function autocomplete ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			$attributes['class'] = 'custom-autocomplete';
			$attributes['data-single'] = 0;
			
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
			
			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}
			
			$attributes['id'] = 'id__' . uniqid();
			if (isset($field['data']) && is_array($field['data'])) {
				$attributes['value'] = [];				
				foreach ($field['data'] as $key) {
					if (isset($field['options'][$key])) {
						$attributes['value'][] = $field['options'][$key];
					}
				}

				$attributes['value'] = Field::arrayToCsv($attributes['value']);
			}

			Field::tag($field, 'input', $attributes);
			
			if (is_array($field['options'])) {
				$tmpJson = [];
				foreach ($field['options'] as $optionKey => $optionValue) {
					$tmpJson[] = array("label" => $optionValue, "value" => $optionKey);
				}
			}
			
			$stringName = '${' . $attributes['id'] . '}';
			//DOMView::addString($stringName, json_encode($tmpJson));
			echo '<div style="display: none" id="', $attributes['id'], '-autocomplete-data">', $stringName, '</div>';	
		};
	}

	public function autocompleteSingle ($attributes=[]) {
		return function ($field) use ($attributes) {
			$attributes['name'] = $field['marker'] . '[' . $field['name'] . ']';
			$tempName = $field['marker'] . '[' . $field['name'] . '_temp]';
			$attributes['class'] .= ' custom-autocomplete';
			$attributes['data-single'] = 1;
				
			if (is_callable($field['options'])) {
				$function = $field['options'];
				$field['options'] = $function();
			};
				
			if (!Field::isAssociative($field['options'])) {
				$field['options'] = Field::forceAssociative($field['options']);
			}
				
			$attributes['id'] = uniqid('id__');
			if (isset($field['data'])) {
				if (is_array($field['data'])) {
					$attributes['value'] = [];
					foreach ($field['data'] as $key) {
						if (isset($field['options'][$key])) {
							$attributes['value'][] = $field['options'][$key];
						}
					}
		
					$attributes['value'] = Field::arrayToCsv($attributes['value']);
				} else {
					if (isset($field['options'][(string)$field['data']])) {
						$attributes['value'] = $field['options'][(string)$field['data']];
					}
				}
			}
	
			Field::tag($field, 'input', array_merge($attributes, ['name' => $tempName]));
			Field::tag($field, 'input', array_merge($attributes, ['type' => 'hidden', 'class' => $attributes['id'], 'id' => uniqid('id__')]));
				
			if (is_array($field['options'])) {
				$tmpJson = [];
				foreach ($field['options'] as $optionKey => $optionValue) {
					$tmpJson[] = array("label" => $optionValue, "value" => $optionKey);
				}
			}
				
			$stringName = '${' . $attributes['id'] . '}';
			//DOMView::addString($stringName, json_encode($tmpJson));
			echo '<div style="display: none" id="', $attributes['id'], '-autocomplete-data">', $stringName, '</div>';
		};
	}
	
	public function filterTags ($attributes=[]) {
		//DOMView::includeFile('/vc/cl/js/admin/vc-filter-tags.js', 5);
		//DOMView::includeFile('/vc/cl/js/admin/vc-filter-tags.css', 5);
		return function ($field) use ($attributes) {
			$tags = Model::mongoDistinct($field['collection'], $field['name']);
			sort($tags);
			$attributes['type'] = 'hidden';
			$field['tagContainerClass'] = false;
			$selected = [];
			if (isset($_SESSION[$field['marker']][$field['name']]) && is_array($_SESSION[$field['marker']][$field['name']]) && count($_SESSION[$field['marker']][$field['name']]) > 0) {
				$selected = $_SESSION[$field['marker']][$field['name']];
			}
			echo '<div class="vc-filter-tags">';
			foreach ($tags as $tag) {
				$class = '';
				$data = '';
                unset($attributes['value']);
				if (in_array($tag, $selected)) {
					$class = ' label-info ';
					$data = $tag;
                    $attributes['value'] = urlencode($data);
				}
				$id = uniqid('tag-');
				echo '<span data-value="', $data, '" data-id="', $id, '" class="label ', $class, '">', $tag, '</span>';
				$attributes['name'] = $field['marker'] . '[' . $field['name'] . '][]';
				$attributes['id'] = $id;
				Field::tag($field, 'input', $attributes);
			}
			echo '</div>';
		};
	}
	
	public function render($callback, $field, $marker) {
		$field['marker'] = $marker;
		ob_start();
		$callback($field);
		return ob_get_clean();
	}
}