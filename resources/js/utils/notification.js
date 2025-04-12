/**
 * Clase Notification para manejar notificaciones estandarizadas usando SweetAlert2.
 * Proporciona métodos estáticos para mostrar mensajes de éxito, error, advertencia, información y confirmaciones.
 */
import Swal from 'sweetalert2';
class Notification {
  /**
   * Opciones predeterminadas para las notificaciones.
   * @property {Object} defaultOptions - Configuración base para las notificaciones.
   * @property {string} defaultOptions.confirmButtonColor - Color del botón de confirmación.
   * @property {string} defaultOptions.cancelButtonColor - Color del botón de cancelación.
   * @property {boolean} defaultOptions.showCancelButton - Indica si se muestra el botón de cancelación.
   * @property {boolean} defaultOptions.showConfirmButton - Indica si se muestra el botón de confirmación.
   * @property {number} defaultOptions.timer - Tiempo en milisegundos para cerrar automáticamente la notificación.
   */
  static defaultOptions = {
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    showCancelButton: false,
    showConfirmButton: true
  };

  /**
   * Muestra una notificación de éxito.
   * @param message - Mensaje que se mostrará en la notificación.
   * @param title - Título de la notificación (opcional).
   * @returns Promesa que se resuelve cuando la notificación se cierra.
   */
  static async success(message, title = 'Éxito!') {
    await this._showAlert(title, message, 'success');
  }

  /**
   * Muestra una notificación de error.
   * @param message - Mensaje que se mostrará en la notificación.
   * @param title - Título de la notificación (opcional).
   * @returns Promesa que se resuelve cuando la notificación se cierra.
   */
  static async error(message, title = 'Error!') {
    await this._showAlert(title, message, 'error');
  }

  /**
   * Muestra una notificación de advertencia.
   * @param message - Mensaje que se mostrará en la notificación.
   * @param title - Título de la notificación (opcional).
   * @returns Promesa que se resuelve cuando la notificación se cierra.
   */
  static async warning(message, title = 'Advertencia!') {
    await this._showAlert(title, message, 'warning');
  }

  /**
   * Muestra una notificación de información.
   * @param message - Mensaje que se mostrará en la notificación.
   * @param title - Título de la notificación (opcional).
   * @returns Promesa que se resuelve cuando la notificación se cierra.
   */
  static async info(message, title = 'Información!') {
    await this._showAlert(title, message, 'info');
  }

  /**
   * Muestra un cuadro de diálogo de confirmación.
   * @param message - Mensaje que se mostrará en el cuadro de diálogo.
   * @param title - Título del cuadro de diálogo (opcional).
   * @param confirmText - Texto del botón de confirmación (opcional).
   * @param cancelText - Texto del botón de cancelación (opcional).
   * @returns Promesa que se resuelve con el resultado de la interacción del usuario.
   */
  static async confirm(
    message,
    title = '¿Estás seguro?',
    confirmText = 'Confirmar',
    cancelText = 'Cancelar'
  ) {
    return Swal.fire({
      ...this.defaultOptions,
      title,
      text: message,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: confirmText,
      cancelButtonText: cancelText,
      reverseButtons: true,
    });
  }

  /**
   * Muestra una notificación genérica.
   * @private
   * @param title - Título de la notificación.
   * @param message - Mensaje que se mostrará en la notificación.
   * @param icon - Icono de la notificación ('success', 'error', 'warning', 'info').
   * @returns Promesa que se resuelve cuando la notificación se cierra.
   */
  static async _showAlert(title, message, icon) {
    await Swal.fire({
      ...this.defaultOptions,
      title,
      text: message,
      icon,
      timer: icon === 'success' ? 2000 : 3000,
    });
  }
}

export default Notification;