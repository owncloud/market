<template lang="pug">
	.uk-card.uk-card-default
		.uk-card-header
			div(uk-grid, class="uk-child-width-1-2@s")
				div
					.uk-flex.uk-flex-middle
						h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom.uk-float-left.uk-margin-small-right {{ app.name }}
						a(href="app.publisher.url", target="_blank").app-author.uk-float-left by {{ app.publisher.name }}
					p.uk-text-meta.uk-margin-remove-top
						span(uk-icon="icon: tag").uk-margin-small-right
						| {{ app.categories[0] }}
				div.uk-text-right
					star-rating(:rating="app.rating")
		.uk-card-media-top
			img(:src="image", :alt="app.title")

		.uk-card-body
			p {{ app.description }}

			table.uk-table
				tr
					th Version
					th Datum
					th Lizenz
				tr
					td {{ app.release.version }}
					td {{ app.release.created | formDate }}
					td {{ app.release.license }}

			div(v-if="!app.release.canInstall", uk-alert).uk-alert-danger
				ul(v-if="!app.release.canInstall").uk-list
					li(v-for="dependency in app.release.missingDependencies")
						span(uk-icon="icon: warning; ratio: 0.75").uk-margin-small-right
						| {{ dependency }}

				p.uk-text-small {{ app.name }} can't be installed due to missing dependencies

		.uk-card-footer
			button.uk-button.uk-button-primary.uk-align-right(:disabled="(app.release.canInstall) ? false : true") Install


</template>

<script>
	export default {

		computed : {
			index () {
				return this.$route.params.id;
			},

			app () {
				return this.$store.getters.apps[this.index];
			},

			platform () {
				return oc_config.version;
			},

			image : function(){
				return this.app.screenshots[0].url;
			}
		},
		filters : {
			formDate (unixtime) {
				return moment(unixtime).format('LL');
			}
		}
	}
</script>

<style>
	.uk-card {
		max-width: 720px;
		margin: 0 auto;
	}

	.app-author {
		margin-top: 6px;
	}
</style>
