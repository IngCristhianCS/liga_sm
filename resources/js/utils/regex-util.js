// regexUtils.js
// Colección de expresiones regulares comunes

const RegexUtils = {
    // Validar correo electrónico
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
  
    // Validar contraseña segura
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?¿¡&_";:\,\+\-()\{\}\[\]\^=#\/\.])([A-Za-z\d$@$!%*?¿¡&_":;\,\+\-()\{\}\[\]\^=#\/\.]|[^ ]){8,16}$/,
   
    // Validar número telefónico (formato internacional)
    phone: /^\+(?:[0-9] ?){6,14}[0-9]$/,
    
    phoneNumeric: /^[0-9]{10}$/,
  
    // Validar URL
    url: /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/,
  
    // Validar fecha (YYYY-MM-DD)
    date: /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/,
  
    // Validar hora (HH:MM)
    time: /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/,
  
    // Validar código postal (México)
    zipCode: /^\d{5}$/,
  
    // Validar números enteros
    integer: /^-?\d+$/,
  
    // Validar números decimales
    decimal: /^-?\d+(\.\d+)?$/,
  
    // Validar solo letras
    lettersOnly: /^[A-Za-z]+$/,
    
    // Validar letras con espacio
    lettersAcuteSpace: /^[A-Za-zá-úÁ-ÚñÑ ]+$/,
  
    // Validar letras y números
    alphanumeric: /^[A-Za-z0-9]+$/,
    
    // Validar letras y números con espacion
    alphanumericSpace: /^[a-zA-Z0-9 ]+$/,
  
    // Validar nombre de usuario
    username: /^[a-zA-Z0-9_-]{3,16}$/,
    
    // Valida estatus
    status: /^[01]$/,
  
    // Validar color hexadecimal
    hexColor: /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/,
  
    // Validar solo números
    numbersOnly: /^\d+$/,
    
    // Texto 
    text: /^[A-Za-zÑñÁáÉéÍíÓóÚúüÜ0-9 ]+$/,
  
    // Eliminar etiquetas HTML
    removeHtmlTags: /<[^>]+>/g,
  
    // Validar dominio
    domain: /^([a-zA-Z0-9]+(-[a-zA-Z0-9]+)*\.)+[a-zA-Z]{2,}$/,
  
    // Validar UUID
    uuid: /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/,
  
    // Validar moneda
    currency: /^\$?\d{1,3}(,\d{3})*(\.\d{1,2})?$/,
  
    // Validar código de color RGB
    rgbColor: /^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/,
  
    // Validar nombre de archivo
    filename: /^[\w,\s-]+\.[A-Za-z]{2,4}$/,
    
    // Texto extendido
    textExtendido: /^[a-zA-Zá-úÁ-ÚñÑ0-9 .,;:()\-_'"¿?¡!@#$%&+/]+$/,
    
    // Números positivos mayor a 0
    numberPositivo: /^0*[1-9]\d*$/,
    
    // RFC
    rfc: /^([A-Z&Ññ]{3}|[A-Z][AEIOU][A-Z]{2})\d{2}((01|03|05|07|08|10|12)(0[1-9]|[12]\d|3[01])|02(0[1-9]|[12]\d)|(04|06|09|11)(0[1-9]|[12]\d|30))([A-Z0-9]{2}[0-9A])$/
    
  };
  
  /**
   * Agrega o elimina la clase 'is-invalid' en un elemento del DOM según el resultado de la validación.
   * 
   * @param elementId - El ID del elemento del DOM al que se aplicará la clase 'is-invalid'.
   * @param esValido - Indica si la validación fue exitosa (`true`) o no (`false`).
   */
  function gestionarClaseInvalid(elementId, esValido) {
      if (elementId) {
          if (!esValido) {			
              $(`#${elementId}`).removeClass('is-valid'); 
              $(`#${elementId}`).addClass('is-invalid'); // Agrega la clase 'is-invalid' si la validación falla
          } else {
              $(`#${elementId}`).removeClass('is-invalid'); // Elimina la clase 'is-invalid' si la validación es exitosa
              $(`#${elementId}`).addClass('is-valid');
          }
      }
  }
  
  function validateField(field, validation, formData) {
      const { regex, optional } = validation[field];
      const value = formData[field];
  
      if (optional && !value) {
          validation[field].valid = true;
      } else {
          validation[field].valid = regex ? regex.test(value) : !!value;
      }
  
      $(`#${field}`).toggleClass('is-invalid', !validation[field].valid);
      return validation[field].valid;
  }
  
  function validateForm(validation, formData) {
      let isValid = true; // Initialize to true
  
      Object.keys(validation).forEach(field => {
          const fieldIsValid = validateField(field, validation, formData);
          if (!fieldIsValid) {
              isValid = false; // Set to false if any field is invalid
              const $element = $(`#${field}`);
              if ($element.length) {
                  $element.parent().find('.select2-container').addClass('is-invalid');
              }
          }
      });
  
      return isValid;
  }
  
  function validarSelect2(selector, valor) {
      if (valor === '' || valor === 0) {
          $(selector).removeClass("is-valid").addClass("is-invalid");
          $(selector).addClass("form-control");
          return false;
      } else {
          $(selector).removeClass("is-invalid").addClass("is-valid");
          return true;
      }
  }
  
  // Export all utilities
  export {
      RegexUtils,
      gestionarClaseInvalid,
      validateField,
      validateForm,
      validarSelect2
  };