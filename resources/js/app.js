import './bootstrap';
//import Alpine from 'alpinejs';

//window.Alpine = Alpine;
//Alpine.start();

// Слушаем событие ошибки от Livewire
window.addEventListener('notify-error', event => {
    alert(event.detail.message);  // Покажет сообщение об ошибке
});
