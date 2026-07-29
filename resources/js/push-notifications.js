import axios from 'axios';

export async function registerServiceWorker() {
  if (!('serviceWorker' in navigator)) return null;
  try {
    const reg = await navigator.serviceWorker.register('/sw.js');
    return reg;
  } catch (e) {
    console.error('SW registration failed', e);
    return null;
  }
}

export async function getVapidPublicKey() {
  const res = await axios.get('/api/webpush/vapid-public-key');
  return res.data;
}

export async function subscribeUser() {
  const reg = await registerServiceWorker();
  if (!reg) throw new Error('Service worker not available');

  const key = await getVapidPublicKey();
  const convertedKey = urlBase64ToUint8Array(key);

  const subscription = await reg.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: convertedKey,
  });

  await axios.post('/api/webpush/subscribe', subscription);
  return subscription;
}

export async function unsubscribeUser() {
  const reg = await registerServiceWorker();
  if (!reg) return;
  const sub = await reg.pushManager.getSubscription();
  if (sub) {
    await axios.post('/api/webpush/unsubscribe', { endpoint: sub.endpoint });
    await sub.unsubscribe();
  }
}

export async function setPushEnabled(enabled) {
  await axios.put('/api/webpush/enabled', { enabled });
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/-/g, '+')
    .replace(/_/g, '/');

  const rawData = atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}
