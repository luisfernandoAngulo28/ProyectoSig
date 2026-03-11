<style type="text/css">
  .thumb-search { margin-right: 10px; }
  .easy-autocomplete-container .eac-item { color: #000; }
  .easy-autocomplete.eac-round ul { top: 0; }
</style>
<script type="text/javascript">
var options = {

  url: function(term) {
    return "{{ url('process/product-bridge-search-own') }}";
  },

  getValue: function(element) {
    return element.id;
  },

  list: {
    match: {
      enabled: false
    },
    onClickEvent: function() {
        var value = $("#search-product-bridge").val();
        $("#search-product-bridge").val('Redireccionando...');
        window.location.replace("{{ url(config('business.product_page')) }}/"+value);
    }
  },

  ajaxSettings: {
    dataType: "json",
    method: "POST",
    data: {
      dataType: "json"
    }
  },

  preparePostData: function(data) {
    data.term = $("#search-product-bridge").val();
    return data;
  },

  template: {
    type: "custom",
    method: function(value, item) {
      return '<img class="thumb-search" width="30" height="30" src="'+item['image']+'">' + item['name'];
    }
  },

  requestDelay: 400,

  theme: "round"
};
$("#search-product-bridge").easyAutocomplete(options);
</script>