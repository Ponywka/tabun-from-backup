<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Сущность связи пользователя и блога
 *
 * @package modules.blog
 * @since 1.0
 */
class ModuleBlog_EntityBlogUser extends Entity
{
    /**
     * Возвращает ID блога
     *
     * @return int|null
     */
    public function getBlogId()
    {
        return $this->_getDataOne('blog_id');
    }
    /**
     * Возвращает ID пользователя
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->_getDataOne('user_id');
    }
    /**
     * Возвращает объект блога
     *
     * @return ModuleBlog_EntityBlog|null
     */
    public function getBlog()
    {
        return $this->_getDataOne('blog');
    }
    /**
     * Возвращает объект пользователя
     *
     * @return ModuleUser_EntityUser|null
     */
    public function getUser()
    {
        return $this->_getDataOne('user');
    }
    /**
     * Устанавливает ID блога
     *
     * @param int $data
     */
    public function setBlogId($data)
    {
        $this->_aData['blog_id']=$data;
    }
    /**
     * Устанавливает ID пользователя
     *
     * @param int $data
     */
    public function setUserId($data)
    {
        $this->_aData['user_id']=$data;
    }
    /**
	 * Устанавливает статус модератора блога
	 *
	 * @param bool $data
	 */
	public function setIsModerator($data) {
		if($data && !$this->getIsModerator()) {
			/**
			 * Повышаем статус до модератора
			 */
			$this->setUserRole(ModuleBlog::BLOG_USER_ROLE_MODERATOR);
		}
	}
	/**
	 * Устанавливает статус администратора блога
	 *
	 * @param bool $data
	 */
	public function setIsAdministrator($data) {
		if($data && !$this->getIsAdministrator()) {
			/**
			 * Повышаем статус до администратора
			 */
			$this->setUserRole(ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);
		}
	}
	/**
	 * Устанавливает роль пользователя
	 *
	 * @param int $data
	 */
	public function setUserRole($data) {
		$this->_aData['user_role']=$data;
	}
	/**
     * Устанавливает блог
     *
     * @param ModuleBlog_EntityBlog $data
     */
    public function setBlog($data)
    {
        $this->_aData['blog']=$data;
    }
    /**
     * Устанавливаем пользователя
     *
     * @param ModuleUser_EntityUser $data
     */
    public function setUser($data)
    {
        $this->_aData['user']=$data;
    }
	/**
	 * Возвращает права пользователя на операции с блогом
	 *
	 * @return Permissions
	 */
	public function getBlogPermissions() {
		$mPerm = $this->_getDataOne('user_blog_permissions');
		isset($mPerm) || $mPerm = $this->legacyRoleToBlogPermissions();
		return new \Permissions((int) $mPerm);
	}
	/**
	 * Возвращает права пользователя на операции с топиками в блоге
	 *
	 * @return Permissions
	 */
	public function getTopicPermissions() {
		$mPerm = $this->_getDataOne('user_topic_permissions');
		isset($mPerm) || $mPerm = $this->legacyRoleToTopicPermissions();
		return new \Permissions((int) $mPerm);
	}
	/**
	 * Возвращает права пользователя на операции с комментами в блоге
	 *
	 * @return Permissions
	 */
	public function getCommentPermissions() {
		$mPerm = $this->_getDataOne('user_comment_permissions');
		isset($mPerm) || $mPerm = $this->legacyRoleToCommentPermissions();
		return new \Permissions((int) $mPerm);
	}
	/**
	 * Возвращает права пользователя на операции с голосами в блоге (ко блогу, к топикам и к комментам)
	 *
	 * @return Permissions
	 */
	public function getVotePermissions() {
		$mPerm = $this->_getDataOne('user_vote_permissions');
		isset($mPerm) || $mPerm = $this->legacyRoleToVotePermissions();
		return new \Permissions((int) $mPerm);
	}

	public function setBlogPermissions($iPerm) {
		$this->_aData['user_blog_permissions'] = $iPerm;
	}
	public function setTopicPermissions($iPerm) {
		$this->_aData['user_topic_permissions'] = $iPerm;
	}
	public function setCommentPermissions($iPerm) {
		$this->_aData['user_comment_permissions'] = $iPerm;
	}
	public function setVotePermissions($iPerm) {
		$this->_aData['user_vote_permissions'] = $iPerm;
	}

	private function legacyRoleToBlogPermissions() {
		switch($this->getUserRole()) {

			case ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR:
				return
					  Permissions::READ
					| Permissions::UPDATE
					| Permissions::DELETE;

			default:
				return
					  Permissions::READ;

		}
	}

	private function legacyRoleToTopicPermissions() {
		switch($this->getUserRole()) {

			case ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR:
				return
					  Permissions::CREATE
					| Permissions::READ
					| Permissions::UPDATE
					| Permissions::DELETE;

			case ModuleBlog::BLOG_USER_ROLE_MODERATOR:
				return
					  Permissions::CREATE
					| Permissions::READ
					| Permissions::UPDATE;

			case ModuleBlog::BLOG_USER_ROLE_USER:
				return
					  Permissions::CREATE
					| Permissions::READ;

			default:
				return
					  Permissions::READ;

		}
	}

	private function legacyRoleToCommentPermissions() {
		switch($this->getUserRole()) {

			case ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR:
				return
					  Permissions::CREATE
					| Permissions::READ
					| Permissions::UPDATE
					| Permissions::DELETE;

			case ModuleBlog::BLOG_USER_ROLE_MODERATOR:
				return
					  Permissions::CREATE
					| Permissions::READ
					| Permissions::DELETE;

			case ModuleBlog::BLOG_USER_ROLE_USER:
				return
					  Permissions::CREATE
					| Permissions::READ;

			default:
				return
					  Permissions::READ;

		}
	}

	private function legacyRoleToVotePermissions() {
		switch($this->getUserRole()) {

			case ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR:
				return
					  Permissions::CREATE
					| Permissions::READ;

			case ModuleBlog::BLOG_USER_ROLE_MODERATOR:
				return
					  Permissions::CREATE
					| Permissions::READ;

			case ModuleBlog::BLOG_USER_ROLE_USER:
				return
					  Permissions::CREATE
					| Permissions::READ;

			default:
				return
					  Permissions::READ;

		}
	}

	/**
	 * Возвращает текущую роль пользователя в блоге старым способом
	 *
	 * @return int|null
	 */
	public function getUserRole() {
		return $this->_getDataOne('user_role');
	}
}
