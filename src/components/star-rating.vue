<template lang="pug">
	ul.uk-padding-remove.uk-margin-remove(uk-tooltip="pos: top-left", :title="overall | textRating")
		li(v-for="n in stars.max").uk-inline-block
			span(:class="(n<=overall) ? '-on' : '-off'",  uk-icon="icon: star; ratio: 0.8").star

</template>

<script>
	export default {
		props: ['rating'],
		data () {
			return {
				classOn : "on",
				classOff : "off",
				overall : this.rating.mean,
				stars : {
					min: 0,
					max: 5
				}
			}
		},
		filter : {
			textRating : function (value) {
				return "Rating: " + value;
			}
		},
		computed: {
			rounded : function () {
				return Math.round(this.overall)
			}
		}
	}
</script>

<style lang="scss">

	@import "../styles/variables-theme";

	.star {
		margin-right: 3px;

		&.-on {
			color: $global-link-color;
		}

		&.-off {
			opacity: 0.25;
		}
	}

</style>