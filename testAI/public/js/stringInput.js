function isAlphanumeric(event) {
    var charCode = event.which || event.keyCode;
    var charStr = String.fromCharCode(charCode);
  
    // Regular expression to allow only numbers and alphabet characters
    var alphanumericRegex = /^[a-zA-Z0-9]+$/;
  
    // Check if the entered character matches the regex pattern
    if (!alphanumericRegex.test(charStr)) {
      event.preventDefault();
      return false;
    }
  }