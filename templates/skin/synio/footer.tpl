				{hook run='content_end'}
			</div> <!-- /content -->
		</div> <!-- /content-wrapper -->
	</div> <!-- /wrapper -->


	
	<footer id="footer">
		<p>Все права принадлежат пони. Весь мир принадлежит пони.</p>
		<div class="text version h-float-right">
            <div>Версия Табуна: <strong>{cfg name='misc.ver.code'}</strong></div>
            <div><a href="https://bitbucket.org/Orhideous/tabun/issues/new">Сообщить об ошибке</a></div>
		</div>
		{hook run='footer_end'}
	</footer>
</div> <!-- /container -->

{include file='toolbar.tpl'}
{if isset($sMarkItUpBundle)}
	<script src="{cfg name='path.static.url'}/{$sMarkItUpBundle}.{cfg name='misc.ver.front'}.bundle.js" type="text/javascript"></script>
{/if}
{hook run='body_end'}

</body>
</html>
