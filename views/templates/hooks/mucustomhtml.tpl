
<div id="mucustomhtml">
	{foreach from=$customhtmlblocks item=i name=block}
		<div class="item{if $smarty.foreach.block.first} first_item{/if}{if $smarty.foreach.block.last} last_item{/if} {$i.cssclass}">
			{$i.htmlcontent}
		</div>
	{/foreach}
	<div class="clear"></div>
</div>