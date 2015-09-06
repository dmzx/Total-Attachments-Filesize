<?php
/**
*
* @package phpBB Extension - Total Attachments Filesize
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\totalattfilesize\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/**
	* Constructor
	* @param \phpbb\template\template			$template
	* @param \phpbb\config\config				$config
	* @param \phpbb\user						$user
	* @param \phpbb\db\driver\driver_interface	$db
	*
	*/
	public function __construct(\phpbb\template\template $template, \phpbb\config\config $config, \phpbb\user $user, \phpbb\db\driver\driver_interface $db)
	{
		$this->template = $template;
		$this->config = $config;
		$this->user = $user;
		$this->db = $db;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header'	=> 'page_header',
		);
	}

	public function page_header($event)
	{
		$this->user->add_lang_ext('dmzx/totalattfilesize', 'common');

		$upload_dir_size = get_formatted_filesize($this->config['upload_dir_size']);

		$sql = 'SELECT SUM(filesize) as stat
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE is_orphan = 0';
		$result = $this->db->sql_query($sql);
		set_config('upload_dir_size', (float) $this->db->sql_fetchfield('stat'), true);
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'UPLOAD_DIR_SIZE'	=> $this->user->lang['UPLOAD_DIR_SIZE'] . ' <strong>' . $upload_dir_size . '</strong>',
		));
	}
}