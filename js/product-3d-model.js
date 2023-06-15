function receiveDataFromPopup(data) {
  console.log("Data received from popup: ", data);
  // process the data as needed...
}

// Listen for messages from the popup

window.addEventListener(
  "message",
  function (event) {
    // Check the origin of the data to ensure it's what you expect
    if (
      (event.origin !== "https://phpstack-947027-3534862.cloudwaysapps.com" ||
      !event.data)
    ) {
      return;
    }
    console.log("Received data: ", JSON.parse(event.data));
    this.localStorage.setItem("customizeData", event.data);
  },
  false
);

function jsonToReadableText(json) {
  let readableText = "Meterial Desing - \n";
  if(json){
	  for (const [key, value] of Object.entries(json)) {
         const material = key.split("-")[1];
         readableText += `${material} : ${value}\n`;
      }
  }
  return readableText;
}

jQuery(document).ready(function ($) {
  $("form.cart").on("submit", function (e) {
    e.preventDefault();
    let materialsJson = JSON.parse(localStorage.getItem("customizeData"));
    let product_id = $(this).find('button[name="add-to-cart"]').val();
    let data = {
      action: "add_custom_data",
      product_id: product_id,
      custom_option: jsonToReadableText(materialsJson),
      quantity: $(this).find('input[name="quantity"]').val(),
    };

    $.post(wc_add_to_cart_params.ajax_url, data, function (response) {
      if (!response) {
        console.log("Invalid response");
        return;
      }

      if (response.error && response.product_url) {
        window.location = response.product_url;
        return;
      }

      localStorage.removeItem("customizeData");
      // Redirect to the cart page
      window.location = wc_add_to_cart_params.cart_url;
    });
  });
});
