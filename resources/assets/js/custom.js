// ATTENCION GULP PRODUTION ERASES console.logs

console.log('custom.js here');

csrf_token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': csrf_token
    }
});

console.log('custom.js here, my ajax setup with csrf_token: ' +csrf_token);