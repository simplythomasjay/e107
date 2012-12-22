<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2011 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *
 *
 * $URL$
 * $Id$
 */

if (!defined('e107_INIT')) { exit; }


class usersettings_shortcodes extends e_shortcode
{
	
	function sc_username($parm) // This is the 'display name'
	{
		$pref = e107::getPref();
		
		if (check_class($pref['displayname_class']) || $pref['allowEmailLogin'] == 1) // display if email is used for login. 
		{
		  	$dis_name_len 	= varset($pref['displayname_maxlength'],15);
			$options 		= array('title'=> LAN_USER_80, 'size' => 40);
		
			return e107::getForm()->text('username',$this->var['user_name'], $dis_name_len, $options);
		}
		else
		{
			return ($parm == 'show') ? $this->var['user_name'] : ''; // ; if it can't be changed then hide it. 
		}
	}
	
	
	
	function sc_loginname($parm)
	{ 
		$pref = e107::getPref();
		
		if($pref['allowEmailLogin'] == 1) // email/password login only. 
		{
			return; // hide login name when email-login is being used. (may contain social login info)	
		}
				
		if (ADMIN && getperms("4"))
		{
			 
		  	$log_name_length = varset($pref['loginname_maxlength'],30);
			
		  	$options = array(
		  		'title'=> ($pref['allowEmailLogin'] ==1 ) ? LAN_USER_82 : LAN_USER_80,
		  		'size' => 40
			);
		
			return e107::getForm()->text('loginname',$this->var['user_loginname'], $log_name_length, $options);	  
		}
		else
		{
			return $this->var['user_loginname'];
		}
	}
	
	
	
	function sc_customtitle($parm)
	{ 	
		if (e107::getPref('signup_option_customtitle'))
		{		
			$options = array('title'=> '', 'size' => 40);	
			return e107::getForm()->text('customtitle', $this->var['user_customtitle'], 100, $options);
		}
	}
	
	
	
	function sc_realname($parm)
	{ 
		$options = array('title'=> '', 'size' => 40);	
		return e107::getForm()->text('realname',$this->var['user_login'], 100, $options);
	}
	
	
	
	function sc_password1($parm)
	{ 
		$pref = e107::getPref();
		
		if(!isset($pref['auth_method']) || $pref['auth_method'] == '' || $pref['auth_method'] == 'e107' || $pref['auth_method'] == '>e107')
		{
			$options = array('size' => 40,'title'=>LAN_USET_23, 'required'=>0); 
			return e107::getForm()->password('password1', '', 20, $options);		
		}
		
		return "";
	}
	
	
	
	function sc_password2($parm)
	{ 
		$pref = e107::getPref();
		
		if(!isset($pref['auth_method']) || $pref['auth_method'] == '' || $pref['auth_method'] == 'e107' || $pref['auth_method'] == '>e107')
		{
			$options = array('size' => 40,'title'=>LAN_USET_23, 'required'=>0); 
			return e107::getForm()->password('password2', '', 20, $options);	
		}
		
		return "";
	}
	
	
	
	function sc_password_len($parm)
	{ 
		$pref = e107::getPref();
		if(!isset($pref['auth_method']) || ($pref['auth_method'] != 'e107' && $pref['auth_method'] != '>e107'))
		{
			return "";
		}
		return $pref['signup_pass_len'];
	}
	
	
	
	function sc_email($parm)
	{ 
		$options = array('size' => 40,'title'=>'','required'=>true); 
		return e107::getForm()->email('email', $this->var['user_email'], 100, $options);
	}
	
	
	
	function sc_hideemail($parm)
	{ 
		if($parm == 'radio')
		{
			$options['enabled'] = array('title' => LAN_USER_84);
			return e107::getForm()->radio_switch("hideemail", $this->var['user_hideemail'],LAN_YES,LAN_NO,$options);		
		}
	}
	
	
	
	function sc_userclasses($parm)
	{ 
		global $e_userclass;
		$tp 		= e107::getParser();
		$pref 		= e107::getPref();
		
		$ret = "";
		if(ADMIN && $this->var['user_id'] != USERID)
		{
			return "";
		}
		if (!is_object($e_userclass)) $e_userclass = new user_class;
		$ucList = $e_userclass->get_editable_classes(USERCLASS_LIST, TRUE);			// List of classes which this user can edit (as array)
		$ret = '';
		if(!count($ucList)) return;
		
		  $is_checked = array();
		  foreach ($ucList as $cid)
		  {
		    if (check_class($cid, $this->var['user_class'])) $is_checked[$cid] = $cid;
			if(isset($_POST['class']))
			{
		//	  $is_checked[$cid] = in_array($cid, $_POST['class']);
			}
		
		  }
		  $inclass = implode(',',$is_checked);
		
	//	  $ret = "<table style='width:95%;margin-left:0px'><tr><td class='defaulttext'>";
		  $ret .= $e_userclass->vetted_tree('class',array($e_userclass,checkbox_desc),$inclass,'editable');
	//	  $ret .= "</td></tr></table>\n";
		
		return $ret;
	}
	
	
	
	function sc_signature($parm)
	{
		$pref = e107::getPref();
		if(!check_class(varset($pref['signature_access'],0)))
		{
			return; 		
		} 
		parse_str($parm);
		$cols = (isset($cols) ? $cols : 58);
		$rows = (isset($rows) ? $rows : 4);
		return "<textarea class='tbox signature' name='signature' cols='{$cols}' rows='{$rows}' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'>".$this->var['user_signature']."</textarea>";
	}
	
	
	
	function sc_signature_help($parm)
	{
		$pref = e107::getPref();
		if(!check_class(varset($pref['signature_access'],0)))
		{
			return; 		
		}  
		return display_help("", 2);
	}
	
	
	
	function sc_avatar_upload($parm) // deprecated and combined into avatarpicker() (see sc_avatar_remote)
	{
		return; 
	}
	
	
	
	function sc_avatar_remote($parm)
	{
		return e107::getForm()->avatarpicker('image',$this->var['user_image'],array('upload'=>1)); 
	}
	
	
	
	function sc_avatar_choose($parm) // deprecated
	{
		return false;
	}
	
	
	
	function sc_photo_upload($parm)
	{ 
		$diz = LAN_USET_27."<br />".LAN_USET_28;
		
		if (e107::getPref('photo_upload') && FILE_UPLOADS)
		{
			return  "<input type='checkbox' name='user_delete_photo' value='1' />".LAN_USET_16."<br />\n
			        <input class='tbox' name='file_userfile[photo]' type='file' size='47'  />
			        <div class='field-help'>{$diz}</div>";
		}
	}
	
	
	
	function sc_userextended_all($parm)
	{ 
		$sql = e107::getDb();
		$tp = e107::getParser();
		
		$qry = "
		SELECT * FROM #user_extended_struct
		WHERE user_extended_struct_applicable IN (".$tp -> toDB($this->var['userclass_list'], true).")
		AND user_extended_struct_write IN (".USERCLASS_LIST.")
		AND user_extended_struct_type = 0
		ORDER BY user_extended_struct_order ASC";
		
		$ret="";
		
		if($sql->db_Select_gen($qry))
		{
			$catList = $sql->db_getList();
		}
		
		$catList[] = array("user_extended_struct_id" => 0, "user_extended_struct_name" => LAN_USET_7);
		
		foreach($catList as $cat)
		{
			cachevars("extendedcat_{$cat['user_extended_struct_id']}", $cat);
			$ret .= $this->sc_userextended_cat($cat['user_extended_struct_id']);
		 // 	$ret .= $tp->parseTemplate("{USEREXTENDED_CAT={$cat['user_extended_struct_id']}}", TRUE, $usersettings_shortcodes);
		}
		
		return $ret;
	}
	
	
	
	function sc_userextended_cat($parm)
	{ 
		global $sql, $tp,  $usersettings_shortcodes, $USER_EXTENDED_CAT, $extended_showed;
		if(isset($extended_showed['cat'][$parm]))
		{
			return "";
		}
		$ret = "";
		$catInfo = getcachedvars("extendedcat_{$parm}");
		if(!$catInfo)
		{
			$qry = "
			SELECT * FROM #user_extended_struct
			WHERE user_extended_struct_applicable IN (".$tp -> toDB($this->var['userclass_list'], true).")
			AND user_extended_struct_write IN (".USERCLASS_LIST.")
			AND user_extended_struct_id = ".intval($parm)."
			";
			if($sql->db_Select_gen($qry))
			{
				$catInfo = $sql->db_Fetch();
			}
		}
		
		if($catInfo)
		{
			$qry = "
			SELECT * FROM #user_extended_struct
			WHERE user_extended_struct_applicable IN (".$tp -> toDB($this->var['userclass_list'], true).")
			AND user_extended_struct_write IN (".USERCLASS_LIST.")
			AND user_extended_struct_parent = ".intval($parm)."
			AND user_extended_struct_type != 0
			ORDER BY user_extended_struct_order ASC
			";
			if($sql->db_Select_gen($qry))
			{
				$fieldList = $sql->db_getList();
				foreach($fieldList as $field)
				{
					cachevars("extendedfield_{$cat['user_extended_struct_name']}", $field);
					//TODO use $this instead of parseTemplate(); 
					$ret .= $tp->parseTemplate("{USEREXTENDED_FIELD={$field['user_extended_struct_name']}}", TRUE, $usersettings_shortcodes);
				}
			}
		}
		
		if($ret)
		{
			$catName = $catInfo['user_extended_struct_text'] ? $catInfo['user_extended_struct_text'] : $catInfo['user_extended_struct_name'];
			if(defined($catName)) $catName = constant($catName);
			$ret = str_replace("{CATNAME}", $tp->toHTML($catName, FALSE, 'emotes_off,defs'), $USER_EXTENDED_CAT).$ret;
		}
		
		$extended_showed['cat'][$parm] = 1;
		return $ret;
	}
	
	
	
	function sc_userextended_field($parm)
	{ 
		global $sql, $tp, $usersettings_shortcodes, $extended_showed, $ue, $USEREXTENDED_FIELD, $REQUIRED_FIELD;
		if(isset($extended_showed['field'][$parm]))
		{
			return "";
		}
		$ret = "";
		
		$fInfo = getcachedvars("extendeddata_{$parm}");
		if(!$fInfo)
		{
			$qry = "
			SELECT * FROM #user_extended_struct
			WHERE user_extended_struct_applicable IN (".$tp -> toDB($this->var['userclass_list'], true).")
			AND user_extended_struct_write IN (".USERCLASS_LIST.")
			AND user_extended_struct_name = '".$tp -> toDB($parm, true)."'
			";
			if($sql->db_Select_gen($qry))
			{
				$fInfo = $sql->db_Fetch();
			}
		}
		
		if($fInfo)
		{
			$fname = $fInfo['user_extended_struct_text'];
			if(defined($fname)) $fname = constant($fname);
			$fname = $tp->toHTML($fname, "", "emotes_off, defs");
			
			if($fInfo['user_extended_struct_required'] == 1)
			{
				$fname = str_replace("{FIELDNAME}", $fname, $REQUIRED_FIELD);
			}
		
			$parms = explode("^,^",$fInfo['user_extended_struct_parms']);
		
			$fhide="";
			if($parms[3])
			{
				$chk = (strpos($this->var['user_hidden_fields'], "^user_".$parm."^") === FALSE) ? FALSE : TRUE;
				if(isset($_POST['updatesettings']))
				{
					$chk = isset($_POST['hide']['user_'.$parm]);
				}
				$fhide = $ue->user_extended_hide($fInfo, $chk);
			}
		
			$uVal = str_replace(chr(1), "", $this->var['user_'.$parm]);
			$fval = $ue->user_extended_edit($fInfo, $uVal);
		
			$ret = $USEREXTENDED_FIELD;
			$ret = str_replace("{FIELDNAME}", $fname, $ret);
			$ret = str_replace("{FIELDVAL}", $fval, $ret);
			$ret = str_replace("{HIDEFIELD}", $fhide, $ret);
		}
		
		$extended_showed['field'][$parm] = 1;
		return $ret;
	}

}
?>