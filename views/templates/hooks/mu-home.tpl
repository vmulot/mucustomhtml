
<div id="mucustomhtml" class="col-xs-12 col-md-4">
	{foreach from=$customhtmlblocks item=htmlblock name=block}
		<div class="{if $smarty.foreach.block.first}first_item{elseif $smarty.foreach.block.last}last_item{else}item{/if}">
			{if $htmlblock.has_picture}
				<div class="image-container">
					<a href="{$htmlblock.link}" title="{$htmlblock.blockname}"><img src="{$img_mu_dir}{$htmlblock.id_mucustomhtml}.jpg" alt="{$htmlblock.blockname}" /></a>
				</div>
			{/if}
			<div class="content">
				{$htmlblock.htmlcontent}
			</div>
			<div class="more">
				<a href="{$htmlblock.link}" title="{$htmlblock.blockname}">{l s='En savoir plus' mod='mucustomhtml'}</a>
			</div>
		</div>
	{/foreach}
</div>