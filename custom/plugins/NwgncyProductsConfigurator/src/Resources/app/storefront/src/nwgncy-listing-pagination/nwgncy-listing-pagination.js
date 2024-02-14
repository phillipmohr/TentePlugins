import ListingPaginationPlugin from 'src/plugin/listing/listing-pagination.plugin';

export default class NwgncyListingPaginationPlugin extends ListingPaginationPlugin {

     onChangePage(event) {
          const url = new URL(window.location.href);
          const searchParams = url.searchParams;
          const params = Object.fromEntries([...searchParams].filter(([key]) => key !== "p"));
          this.tempValue = event.target.value;
          this.listing.changeListing(true, {...params});
          this.tempValue = null;
     }
}