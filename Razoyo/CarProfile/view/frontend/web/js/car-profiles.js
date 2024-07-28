define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        const token = config.token;
        const selectedCarId = config.selectedCarId;

        function updateCarDetails(carId, token) {
            $.ajax({
                url: 'account/save',
                type: 'GET',
                data: { 
                    carId: carId,
                    token: token
                },
                showLoader: true,
                cache: false,
                success: function (response) {
                    if (response.success && response.carDetails) {
                        const car = response.carDetails.car;
                        const carDetailsHtml = `
                            <legend class="legend"><span>Car Details</span></legend>
                            <div class="car-info">
                                <div class="car-image">
                                    <img src="${car.image}" 
                                         alt="${car.make} ${car.model}"
                                         class="car-photo">
                                </div>
                                <div class="car-specs">
                                    <ul class="car-attributes">
                                        ${['year', 'make', 'model', 'price', 'seats', 'mpg'].map(attr => `
                                            <li>
                                                <span class="label">${attr.charAt(0).toUpperCase() + attr.slice(1)}:</span>
                                                <span class="value">${car[attr]}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>`;
                        $('#car-details').html(carDetailsHtml).show();
                        $('#no-car-selected').hide();
                    } else {
                        $('#car-profile-messages').append('<div class="alert alert-danger auto-dismiss">' + response.message + '</div>');
                        $('#car-details').hide();
                        $('#no-car-selected').show();
                    }
                }
            });
        }

        if (selectedCarId) {
            updateCarDetails(selectedCarId, token);
        }

        $("#car-select").on('change', function() {
            const selectedVal = $(this).val();
            $.ajax({
                url: 'account/save',
                type: 'POST',
                data: { car: selectedVal },
                showLoader: true,
                cache: false,
                success: function (response) {
                    const messagesDiv = $("#car-profile-messages");
                    messagesDiv.empty();
                    if (response.success) {
                        messagesDiv.append('<div class="alert alert-success auto-dismiss">' + response.message + '</div>');
                        updateCarDetails(selectedVal, token);
                    } else {
                        messagesDiv.append('<div class="alert alert-danger auto-dismiss">' + response.message + '</div>');
                    }
                }
            });
        });
    };
});