{if $views}
	<div class="articleViews_wrapper">
		<span class="views article_views">{translate key="plugins.generic.articleViews.article.views"}</span>
		<span class="count article_views">{$views}</span>
		{if $galleyViews}
			<span class="delimiter">|</span>
			<span class="views galley_views">{translate key="plugins.generic.articleViews.galleys.views"}</span>
			<span class="count galley_views">{$galleyViews}</span>
		{/if}
	</div>
{/if}
