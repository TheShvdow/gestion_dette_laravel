import { getCurrentInstance } from 'vue';

let toastInstance = null;

export function useToast() {
  // Get the current component instance
  const instance = getCurrentInstance();

  // If we're in a component, try to get the toast from the root
  if (instance && instance.appContext.config.globalProperties.$toast) {
    return instance.appContext.config.globalProperties.$toast;
  }

  // Return the stored instance if available
  if (toastInstance) {
    return toastInstance;
  }

  // Return a dummy implementation if toast is not available
  return {
    success: () => {},
    error: () => {},
    warning: () => {},
    info: () => {},
  };
}

export function setToastInstance(instance) {
  toastInstance = instance;
}
