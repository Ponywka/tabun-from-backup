{foreach from=$aReplyWall item=oReplyWall}
	{assign var="oReplyUser" value=$oReplyWall->getUser()}
	<div>
	{$oReplyUser->getLogin()} {date_format date=$oReplyWall->getDateAdd() format="j F Y, H:i"}
		<br>
	{$oReplyWall->getText()} - ({$oReplyWall->getId()})
	</div>
{/foreach}