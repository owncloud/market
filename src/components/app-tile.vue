<template lang="pug">
	li(v-if="!app.installed", class='uk-width-1-2@m uk-width-1-3@l')
		.uk-card.uk-card-default
			.uk-card-media-top
				img(:src="image", :alt="title")
			.uk-card-header
				h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom {{ title }}
				p.uk-text-meta.uk-margin-remove-top
					span(uk-icon="icon: tag").uk-margin-small-right
					| {{ app.categories[0] }}
			.uk-card-body
				p {{ app.description }}
				div(uk-grid, class="uk-child-width-1-2@s")
					div
						StarRating(:rating="app.rating")
					div
						button(@click="openDetails").uk-button.uk-button-default.uk-button-small.uk-align-right More
</template>

<script>

	import StarRating from './star-rating.vue';
	import DetailModal from './detail-modal.vue';

	export default {
		components: {
			StarRating,
			DetailModal
		},
		props : ['app'],
		data () {
			return {
				title : this.app.name,
				stars : this.app
			}
		},
		methods: {
			openDetails: function () {
			}
		},
		computed : {
			image : function(){

				// TODO: replace with actual screenshot when available

				let x = 0;
				let y = 1000;
				let no = Math.floor(Math.random() * ((y-x)+1) + x);
				let image = 'https://unsplash.it/800/450/?image=' + no;

				return image;


				// this is the correct screenshot
				// return this.app.screenshots[0];
			}
		}

	}
</script>

<style lang="scss" scoped>

	@import "../styles/variables-theme";

	.uk-list {
		li:not(:last-child) {
			margin-right: $global-link-color;
		}
	}
</style>