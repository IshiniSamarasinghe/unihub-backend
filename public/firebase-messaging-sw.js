// firebase-messaging-sw.js

/* global importScripts, firebase */
importScripts("https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js");

// Firebase config (same as in firebase.js)
firebase.initializeApp({
  apiKey: "AIzaSyCN1fvaeWiI4FFb6dWCl5O2iQ3nTAyZkrs",
  authDomain: "unihub-notifications.firebaseapp.com",
  projectId: "unihub-notifications",
  storageBucket: "unihub-notifications.appspot.com", // ✅ corrected here
  messagingSenderId: "277803908490",
  appId: "1:277803908490:web:dfc76f1fc1a3ddf543dc73"
});

const messaging = firebase.messaging();

// Optional: Handle background messages
messaging.onBackgroundMessage(function (payload) {
  console.log("[firebase-messaging-sw.js] Received background message ", payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: "/logo192.png",
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
