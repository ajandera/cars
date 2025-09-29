{if !empty($thisObrazky)}
<div class="gallery">
	{foreach $thisObrazky as $k=>$v}
		{if $v.extension == 'jpg' || $v.extension == 'jpeg' || $v.extension == 'png' || $v.extension == 'gif'}
			<a href="{$v.secure_download}" data-lightbox="gallery" data-title="{$k}">
				<img src="/{GetImageThumb src=$v.obrazek width="250" height="250" crop="true"}" loading="lazy" class="img-fluid img-responsive">
			</a>
		{else if $v.extension == 'mp4'}
			<video
				id="{$k}"
				class="video-js"
				controls
				preload="auto"
				width="250"
				height="250"
				data-setup="{}"
			>
				<source src="{$v.secure_download}" type="video/mp4" />
			</video>
		{else}
		<a href="{$v.secure_download}" data-lightbox="gallery" data-title="{$k}">
			<span><i class="fa fa-file-text-o fa-4x fa-fw" aria-hidden="true"></i><br />{$v.filename}</span>
		</a>
		{/if}
		
{/foreach}
</div>
{/if}