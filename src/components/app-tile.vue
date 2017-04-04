<template lang="pug">
	li
		.uk-card.uk-card-default(class='uk-width-1-2@m')
			.uk-card-header
				.uk-grid-small.uk-flex-middle(uk-grid='')
					.uk-width-auto
						img.uk-border-circle(width='40', height='40', :src="image")
					.uk-width-expand
						h3.uk-card-title.uk-margin-remove-bottom {{ title }}
						p.uk-text-meta.uk-margin-remove-top {{ app.description }}
			.uk-card-body
				p Body here
			.uk-card-footer
				a.uk-button.uk-button-text(href='#') Read more
</template>

<script>

	export default {
		props : ['app'],
		data () {
			return { title : this.app.id }
		},
		computed : {
			image : function(){

				// TODO: replace with actual screenshot when available
				return 'https://unsplash.it/500/500/?random'

				// this is the correct screenshot
				// return this.app.screenshots[0];
			}
		}

	}
</script>