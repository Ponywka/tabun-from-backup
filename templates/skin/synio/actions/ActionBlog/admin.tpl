{include file='header.tpl'}
{include file='menu.blog_edit.tpl'}



{if $aBlogUsers}
	<form method="post" enctype="multipart/form-data" class="mb-20">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
		
		<table class="table table-users">
			<thead>
				<tr>
					<th class="cell-name">{$aLang.blog_admin_users}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_blog}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_topics}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_comments}</th>
					<th class="ta-c">{$aLang.blog_admin_users_perm_votes}</th>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$aBlogUsers item=oBlogUser}
					{assign var="oUser" value=$oBlogUser->getUser()}
					{assign var="oUserBlogPermissions"    value=$oBlogUser->getBlogPermissions()}
					{assign var="oUserTopicPermissions"   value=$oBlogUser->getTopicPermissions()}
					{assign var="oUserCommentPermissions" value=$oBlogUser->getCommentPermissions()}
					{assign var="oUserVotePermissions"    value=$oBlogUser->getVotePermissions()}
					
					<tr>
						<td class="cell-name">
							<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" class="avatar" /></a>
							<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
						</td>
						
						{if $oUser->getId()==$oUserCurrent->getId()}
							<td colspan="3">{$aLang.blog_admin_users_current_administrator}</td>
						{else}
							<td class="ta-c">
								<input type="checkbox" name="user_perm[{$oUser->getId()}][blog_update]" {if $oUserBlogPermissions->check(Permissions::UPDATE)}checked{/if} title="{$aLang.blog_admin_users_perm_blog_update}" />
								<input type="checkbox" name="user_perm[{$oUser->getId()}][blog_delete]" {if $oUserBlogPermissions->check(Permissions::DELETE)}checked{/if} title="{$aLang.blog_admin_users_perm_blog_delete}" />
							</td>
							<td class="ta-c">
								<input type="checkbox" name="user_perm[{$oUser->getId()}][topics_create]" {if $oUserTopicPermissions->check(Permissions::CREATE)}checked{/if} title="{$aLang.blog_admin_users_perm_topics_create}" />
								<input type="checkbox" name="user_perm[{$oUser->getId()}][topics_read]"   {if $oUserTopicPermissions->check(Permissions::READ  )}checked{/if} title="{$aLang.blog_admin_users_perm_topics_read}" />
								<input type="checkbox" name="user_perm[{$oUser->getId()}][topics_update]" {if $oUserTopicPermissions->check(Permissions::UPDATE)}checked{/if} title="{$aLang.blog_admin_users_perm_topics_update}" />
								<input type="checkbox" name="user_perm[{$oUser->getId()}][topics_delete]" {if $oUserTopicPermissions->check(Permissions::DELETE)}checked{/if} title="{$aLang.blog_admin_users_perm_topics_delete}" />
							</td>
							<td class="ta-c">
								<input type="checkbox" name="user_perm[{$oUser->getId()}][comments_create]" {if $oUserCommentPermissions->check(Permissions::CREATE)}checked{/if} title="{$aLang.blog_admin_users_perm_comments_create}" />
								<input type="checkbox" name="user_perm[{$oUser->getId()}][comments_read]"   {if $oUserCommentPermissions->check(Permissions::READ  )}checked{/if} title="{$aLang.blog_admin_users_perm_comments_read}" />
								<input type="checkbox" name="user_perm[{$oUser->getId()}][comments_update]" {if $oUserCommentPermissions->check(Permissions::UPDATE)}checked{/if} title="{$aLang.blog_admin_users_perm_comments_update}" />
								<input type="checkbox" name="user_perm[{$oUser->getId()}][comments_delete]" {if $oUserCommentPermissions->check(Permissions::DELETE)}checked{/if} title="{$aLang.blog_admin_users_perm_comments_delete}" />
							</td>
							<td class="ta-c">
								<input type="checkbox" name="user_perm[{$oUser->getId()}][votes_create]" {if $oUserVotePermissions->check(Permissions::CREATE)}checked{/if} title="{$aLang.blog_admin_users_perm_votes_create}" />
							</td>
						{/if}
					</tr>
				{/foreach}
			</tbody>
		</table>

		<button type="submit"  name="submit_blog_admin" class="button button-primary">{$aLang.blog_admin_users_submit}</button>
	</form>

	{include file='paging.tpl' aPaging=$aPaging}
{else}
	{$aLang.blog_admin_users_empty}
{/if}



{include file='footer.tpl'}
