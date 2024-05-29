( function( api ) {

	// Extends our custom "cosmetic-store" section.
	api.sectionConstructor['cosmetic-store'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );