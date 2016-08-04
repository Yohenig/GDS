<?php 

/*
// mod for K2 Extra fields Filter and Search module by Piotr Konieczny
// piotr@smartwebstudio.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');


class modK2FilterHelper {

	// pulls out specified information about extra fields from the database
	function pull($field_id,$what) {
		$query = 'SELECT t.id, t.name as name, t.value as value, t.type as type FROM #__k2_extra_fields AS t WHERE t.published = 1 AND t.id = "'.$field_id.'"';
		$db = &JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObject();
		
		if($result) {
			$extra_fields = get_object_vars($result);
		
			switch ($what) {
				case 'name' :
					$output = $extra_fields['name']; break;
				case 'type' :
					$output = $extra_fields['type']; break;
				case 'value' :
					$output = $extra_fields['value']; break;
				default:
					$output = $extra_fields['value']; break;
			}
		}
		else {
			$output = "";
		}
		
		return $output;
	}
	
	function pullNotTranslated($field_id,$what) {
		$query = 'SELECT t.name as name, t.value as value, t.type as type FROM #__k2_extra_fields AS t WHERE t.published = 1 AND t.id = "'.$field_id.'"';
		$db = &JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObject();
		
		if($result) {
			$extra_fields = get_object_vars($result);
		
			switch ($what) {
				case 'name' :
					$output = $extra_fields['name']; break;
				case 'type' :
					$output = $extra_fields['type']; break;
				case 'value' :
					$output = $extra_fields['value']; break;
				default:
					$output = $extra_fields['value']; break;
			}
		}
		else {
			$output = "";
		}
		
		return $output;
	}
	
	// pulls out extra fields of specified item from the database
	function pullItem($itemID) {
		$query = 'SELECT t.id, t.extra_fields FROM #__k2_items AS t WHERE t.published = 1 AND t.id = "'.$itemID.'"';
		$db = &JFactory::getDBO();
		$db->setQuery($query);
		$extra_fields = get_object_vars($db->loadObject());
		$output = $extra_fields['extra_fields'];
		return $output;
	}
	
	// extracts info from JSON format
	function extractExtraFields($extraFields) {		
		$jsonObjects = json_decode($extraFields);

		if (count($jsonObjects)<1) return NULL;

		// convert objects to array
		foreach ($jsonObjects as $object){
			if (isset($object->name)) {
				$objects[$object->value] = $object->name;
			}
			else if (isset($object->id)) {
				$objects[$object->id] = $object->value;
			}
			else return;
		}
		return $objects;
	}
	
	// from thaderweb.com
	function getExtraField($id){
		$db	=	JFactory::getDBO();
		$query	=	"SELECT id, name, value FROM #__k2_extra_fields WHERE id = $id";
		$db->setQuery($query);
		$rows	=	$db->loadObject();

		return $rows;
	}
	
	function getTags(&$params, $restcata = 0) {
		
		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = &JFactory::getDBO();

		$jnow = &JFactory::getDate();
		$now = $jnow->toSQL();
		$nullDate = $db->getNullDate();

		$query = "SELECT i.id FROM #__k2_items as i";
		$query .= " LEFT JOIN #__k2_categories c ON c.id = i.catid";
		$query .= " WHERE i.published=1 ";
		$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." ) ";
		$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";
		$query .= " AND i.trash=0 ";

		$query .= " AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";

		$query .= " AND c.published=1 ";
		$query .= " AND c.trash=0 ";

		$query .= " AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";

		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$tagCategory = $params->get('restcat');
				$tagCategory = str_replace(" ", "", $tagCategory);
				$tagCategory = explode(",", $tagCategory);
				if(is_array($tagCategory)) {
					$tagCategory = array_filter($tagCategory);
				}
				if ($tagCategory) {
					if(!is_array($tagCategory)){
						$tagCategory = (array)$tagCategory;
					}
					foreach($tagCategory as $tagCategoryID){
						$categories[] = $tagCategoryID;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($tagCategoryID);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					if(count($categories)==1){
						$query .= " AND i.catid={$categories[0]}";
					}
					else {
						$query .= " AND i.catid IN(".implode(',', $categories).")";
					}
				}
			}
			
			else if($params->get('restmode') == 1) {
			
				$tagCategory = $restcata;
				if(is_array($tagCategory)) {
					$tagCategory = array_filter($tagCategory);
				}
				if ($tagCategory) {
					if(!is_array($tagCategory)){
						$tagCategory = (array)$tagCategory;
					}
					foreach($tagCategory as $tagCategoryID){
						$categories[] = $tagCategoryID;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($tagCategoryID);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					if(count($categories)==1){
						$query .= " AND i.catid={$categories[0]}";
					}
					else {
						$query .= " AND i.catid IN(".implode(',', $categories).")";
					}
				}
			
			}
		}
		

		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$db->setQuery($query);
		$IDs = K2_JVERSION == '30' ? $db->loadColumn() : $db->loadResultArray();
		
		$rows = Array();
		if($IDs) {
			$query = "SELECT tag.name, tag.id
			FROM #__k2_tags as tag
			LEFT JOIN #__k2_tags_xref AS xref ON xref.tagID = tag.id 
			WHERE xref.itemID IN (".implode(',', $IDs).") 
			AND tag.published = 1 ORDER BY tag.name ASC";
			
			$db->setQuery($query);
			$rows = K2_JVERSION == '30' ? $db->loadColumn() : $db->loadResultArray();
			$cloud = array();
		}
		
		if (count($rows)) {
			
			foreach ($rows as $tag) {
				if (@array_key_exists($tag, $cloud)) {
					$cloud[$tag]++;
				} else {
					$cloud[$tag] = 1;
				}
			}

			$counter = 0;
			foreach ($cloud as $key=>$value) {
				$tags[$counter]-> {'tag'} = $key;
				$counter++;
			}

			return $tags;
		}
	}
	
	function getCategoryChildren($catid) {

		static $array = array();
		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$catid = (int) $catid;
		$db = &JFactory::getDBO();
		$query = "SELECT * FROM #__k2_categories WHERE parent={$catid} AND published=1 AND trash=0 ";

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY ordering ";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		foreach ($rows as $row) {
			array_push($array, $row->id);
			if (modK2FilterHelper::hasChildren($row->id)) {
				modK2FilterHelper::getCategoryChildren($row->id);
			}
		}
		return $array;
	}
	
	function hasChildren($id) {

		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$id = (int) $id;
		$db = &JFactory::getDBO();
		$query = "SELECT * FROM #__k2_categories  WHERE parent={$id} AND published=1 AND trash=0 ";

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		if (count($rows)) {
			return true;
		} else {
			return false;
		}
	}
	
	function treeselectbox(&$params, $id = 0, $level = 0, $i, $moduleId) {

		$mainframe = &JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		
		$root_id = Array();
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$root_id = $params->get('restcat');
				$root_id = str_replace(" ", "", $root_id);
			}
			else if($params->get('restmode') == 1) {
				if($view == "itemlist" && $task == "category") 
					$root_id = JRequest::getInt("id");
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$root_id = modK2FilterHelper::getParent($id);
				}
				else {
					$root_id = JRequest::getVar("restcata");
				}
			}
			$root_id = explode(",", $root_id);
		}
		
		$category = JRequest::getInt('category');
		if($category == 0 && $option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		$id = (int) $id;
		$user = JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = JFactory::getDBO();
		
		if (count($root_id) && ($level == 0)) {
			$query = "SELECT * FROM #__k2_categories WHERE id IN(" . implode(",", $root_id) . ") AND published = 1 AND trash = 0 ";
		} else {
			$query = "SELECT * FROM #__k2_categories WHERE parent = {$id} AND published = 1 AND trash = 0 ";
		}

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY ordering";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		$indent = "";
		for ($i = 0; $i < $level; $i++) {
			$indent .= '&ndash; ';
		}
		
		foreach ($rows as $k => $row) {
			if (($option == 'com_k2') && ($category == $row->id)) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			if (modK2FilterHelper::hasChildren($row->id)) {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
				if($params->get('restsub') == 1) {
					modK2FilterHelper::treeselectbox($params, $row->id, $level + 1, $i, $moduleId);
				}
			} else {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
			}
		}
	}
	
	function treeselectbox_multi(&$params, $id = 0, $level = 0, $i, $elems, $moduleId) {

		$mainframe = &JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		
		$root_id = Array();
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$root_id = $params->get('restcat');
				$root_id = str_replace(" ", "", $root_id);
			}
			else if($params->get('restmode') == 1) {
				if($view == "itemlist" && $task == "category") 
					$root_id = JRequest::getInt("id");
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$root_id = modK2FilterHelper::getParent($id);
				}
				else {
					$root_id = JRequest::getVar("restcata");
				}
			}
			$root_id = explode(",", $root_id);
		}
		
		$category = JRequest::getInt('category');
		if($category == 0 && $option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		$id = (int) $id;
		$user = JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = JFactory::getDBO();
		
		if (count($root_id) && ($level == 0)) {
			$query = "SELECT * FROM #__k2_categories WHERE id IN(" . implode(",", $root_id) . ") AND published = 1 AND trash = 0 ";
		} else {
			$query = "SELECT * FROM #__k2_categories WHERE parent = {$id} AND published = 1 AND trash = 0 ";
		}

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY ordering";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		
		$indent = "";
		for ($i = 0; $i < $level; $i++) {
			$indent .= '&nbsp&ndash;';
		}
		
		foreach ($rows as $k => $row) {
		
			if($elems > 0 && ($k+1) > $elems && @$cat_switch == 0 && $level == 0) {
				echo "<div class='filter_cat_hidden'>";
				$cat_switch = 1;
			}
		
			if ($option == 'com_k2') {
				
				$category_search = JRequest::getVar("category");
				$selected = '';
				
				if(is_array($category_search) == true) {
					foreach($category_search as $cat) {
						if($cat == $row->id)
							$selected = ' checked="checked"';
					}
				}
				else {
					if($category_search == $row->id)
							$selected = ' checked="checked"';
				}
			} else {
				$selected = '';
			}
			
			$onchange = $params->get('onchange', 0);
			if (modK2FilterHelper::hasChildren($row->id)) {
				echo $indent.'<input name="category[]" type="checkbox" value="'.$row->id.'"'.$selected.' id="'.$row->name . $row->id . '"';
				if($onchange) {
					echo " onchange='submit_form_".$moduleId."()'";
				}
				echo ' />';
				echo '<label for="'.$row->name.$row->id.'">'.$row->name.'</label><br />';
				if($params->get('restsub') == 1) {
					modK2FilterHelper::treeselectbox_multi($params, $row->id, $level + 1, $i, $elems, $moduleId);
				}
			} else {
				echo $indent.'<input name="category[]" type="checkbox" value="'.$row->id.'"'.$selected.' id="'.$row->name . $row->id . '"';
				if($onchange) {
					echo " onchange='submit_form_".$moduleId."()'";
				}
				echo '/>';
				echo '<label for="'.$row->name.$row->id.'">'.$row->name.'</label><br />';
			}
		}
	}
	
	function treeselectbox_multi_select(&$params, $id = 0, $level = 0, $i, $elems, $moduleId) {

		$mainframe = &JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		
		$root_id = Array();
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$root_id = $params->get('restcat');
				$root_id = str_replace(" ", "", $root_id);
			}
			else if($params->get('restmode') == 1) {
				if($view == "itemlist" && $task == "category") 
					$root_id = JRequest::getInt("id");
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$root_id = modK2FilterHelper::getParent($id);
				}
				else {
					$root_id = JRequest::getVar("restcata");
				}
			}
			$root_id = explode(",", $root_id);
		}
		
		$category = JRequest::getInt('category');
		if($category == 0 && $option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		$id = (int) $id;
		$user = JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = JFactory::getDBO();
		
		if (count($root_id) && ($level == 0)) {
			$query = "SELECT * FROM #__k2_categories WHERE id IN(" . implode(",", $root_id) . ") AND published = 1 AND trash = 0 ";
		} else {
			$query = "SELECT * FROM #__k2_categories WHERE parent = {$id} AND published = 1 AND trash = 0 ";
		}

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY ordering";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		
		$indent = "";
		for ($i = 0; $i < $level; $i++) {
			$indent .= '&ndash; ';
		}
		
		if($option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		foreach ($rows as $k => $row) {
			$selected = '';
			if ($category == $row->id) {
				$selected = ' selected="selected"';
			} else {
				$category_search = JRequest::getVar("category");
				foreach($category_search as $catid) {
					if($catid == $row->id) {
						$selected = ' selected="selected"';
					}
				}
			}
			
			if (modK2FilterHelper::hasChildren($row->id)) {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
				if($params->get('restsub') == 1) {
					modK2FilterHelper::treeselectbox_multi_select($params, $row->id, $level + 1, $i, $moduleId);
				}
			} else {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
			}
		}
	}
	
	function getParent($id) {
		$db = &JFactory::getDBO();
		
		$query = "SELECT * FROM #__k2_items WHERE id = {$id}";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->catid;
	}
	
	function getModuleParams($id) {
		$db =& JFactory::getDBO();
		
		$query = "SELECT * FROM #__modules WHERE id = {$id}";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		$moduleParams = json_decode($result->params);
		return $moduleParams;
	}
	
	function getAuthors(&$params) {
		$mainframe = &JFactory::getApplication();
		$componentParams = &JComponentHelper::getParams('com_k2');
		$where = '';
		
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$catids = $params->get('restcat');
				$catids = str_replace(" ", "", $catids);
				$catids = explode(",", $catids);
				
				if(is_array($catids)) {
					$catids = array_filter($catids);
				}
				
				if ($catids) {
					if(!is_array($catids)){
						$catids = (array)$catids;
					}
					foreach($catids as $catid){
						$categories[] = $catid;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($catid);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					
					if(count($categories) == 1){
						$where = " catid={$categories[0]} AND ";
					}
					else {
						$where = " catid IN(".implode(',', $categories).") AND";
					}
				}
			}
			else if($params->get('restmode') == 1) {
				$catid = JRequest::getVar("restcata");			
				$where = " catid = {$catid} AND ";			
			}
		}
				
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = &JFactory::getDBO();

		$jnow = &JFactory::getDate();
		$now = $jnow->toSQL();
		$nullDate = $db->getNullDate();


		$languageCheck = '';
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$languageCheck = "AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
		}
		$query = "SELECT DISTINCT created_by FROM #__k2_items
        WHERE {$where} published=1 
        AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= ".$db->Quote($now)." ) 
        AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= ".$db->Quote($now)." ) 
        AND trash=0 
        AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") 
        AND created_by_alias='' 
		{$languageCheck}
        AND EXISTS (SELECT * FROM #__k2_categories WHERE id= #__k2_items.catid AND published=1 AND trash=0 AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") {$languageCheck})";        	

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$authors = array();
		if (count($rows)) {
			foreach ($rows as $row) {
				$author = JFactory::getUser($row->created_by);
				if($author->block == 1) continue;
				
				$author->link = JRoute::_(K2HelperRoute::getUserRoute($author->id));

				$query = "SELECT id, gender, description, image, url, `group`, plugins FROM #__k2_users WHERE userID=".(int)$author->id;
				$db->setQuery($query);
				$author->profile = $db->loadObject();

				$authors[] = $author;
			}
		}
		
		return $authors;
	}
	
	function getChildsCount($extras, $name) {
		$count = 0;
		foreach($extras as $title) {
			if(stripos($title, trim($name)) !== FALSE) {
				$count++;
			}
		}
		return $count;
	}
	
	function getExtraValues($id, $moduleParams) {
		require_once(JPATH_BASE.DS.'plugins'.DS.'system'.DS.'k2filter'.DS.'K2Filter'.DS.'models'.DS.'itemlistfilter.php');
		$mydb = JFactory::getDBO();
		$myquery = "SELECT i.* FROM #__k2_items as i WHERE published = 1 AND trash = 0";
		
		if ($moduleParams->get("restrict") == 1) {
			if ($moduleParams->get("restmode") == 0 && $moduleParams->get("restcat") != '') {
				$restcat = $moduleParams->get("restcat");
				$restcat = str_replace(" ", "", $restcat);
				$restcat = explode(",", $restcat);
						
				$restsub = $moduleParams->get("restsub");
						
				if($restsub == 1) {
					$myquery .= " AND ( ";
					foreach($restcat as $kr => $restcatid) {
						$restsubs = K2ModelItemListFilter::getCategoryTree($restcatid);
						foreach($restsubs as $k => $rests) {
							$myquery .= "i.catid = " . $rests;
							if($k+1 < sizeof($restsubs))
								$myquery .= " OR ";
						}
						if($kr+1 < sizeof($restcat))
							$myquery .= " OR ";			
					}
					$myquery .= " )";
				}
				else {
					$myquery .= " AND ( ";
					foreach($restcat as $kr => $restcatid) {
						$myquery .= "i.catid = " . $restcatid;
						if($kr+1 < sizeof($restcat))
							$myquery .= " OR ";			
					}
					$myquery .= " )";
				}
			}	
			else if ($moduleParams->get("restmode") == 1 && JRequest::getVar("restcata") != "") {
				$restcata = JRequest::getVar('restcata');
				$restsub = $moduleParams->get("restsub");
					
				if($restsub == 1) {
					$myquery .= " AND ( ";
					$restsubs = K2ModelItemListFilter::getCategoryTree($restcata);
					foreach($restsubs as $k => $rests) {
						$myquery .= "i.catid = " . $rests;
						if($k+1 < sizeof($restsubs))
							$myquery .= " OR ";
					}
					$myquery .= " )";
				}
				else 
					$myquery .= " AND i.catid = " . $restcata;
			}
		}

		$mydb->setQuery($myquery);
		$items = $mydb->loadObjectList();
		
		$values = Array();
		$field_type = modK2FilterHelper::pull($id, 'type');

		foreach($items as $item) {
			if($item->extra_fields) {
				$extras = json_decode($item->extra_fields);				
				foreach($extras as $field) {
					if($field->id == $id && $field->value != '') {
						if($field_type == 'multipleSelect' || $field_type == 'select') {
							$extraVals = modK2FilterHelper::getExtraValsByIndexes($id, $field->value);
							$values = array_merge($values, $extraVals);
						}
						else {
							array_push($values, $field->value);
						}
					}
				}
			}
		}

		sort($values);
		return array_values(array_unique($values));
	}
	
	function getExtraValsByIndexes($extra_id, $indexes) {
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__k2_extra_fields WHERE id = {$extra_id}";
		$db->setQuery($query);
		$extra = $db->loadObject();
				
		$values = json_decode($extra->value);
		
		$result = Array();
		if(!is_array($indexes)) {
			$indexes = Array($indexes);
		}
		
		foreach($values as $value) {
			if(in_array($value->value, $indexes)) {
				$result[] = $value->name;
			}
		}
		
		return $result;	
	}
	
}
