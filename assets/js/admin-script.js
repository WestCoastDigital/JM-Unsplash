jQuery(document).ready(function ($) {

    let currentPage = 1;
    let totalPages = 1;
    let apiKey = jmUnsplashApi;

    $('#image-search-form').on('submit', function (e) {
        e.preventDefault();
        let searchQuery = $('#image_search').val();
        let perPage = jmUnsplashPerPay;
        let apiUrl = 'https://api.unsplash.com/search/photos/?client_id=' + apiKey + '&query=' + searchQuery + '&per_page=' + perPage;

        $.ajax({
            url: apiUrl,
            type: 'GET',
            success: function (response) {
                totalPages = response.total_pages;
                if (response.results.length > 0) {
                    let images = response.results;
                    let imageContainer = $('#image-results');

                    // Clear previous search results
                    imageContainer.empty();
                    $buttons = $('.image-search-container').find('button');
                    $buttons.each(function () {
                        $(this).remove();
                    });


                    let button = $('<button>').addClass('button button-primary').text('No Images Selected');
                    button.attr('type', 'button');
                    button.attr('id', 'use-images');
                    button.attr('disabled', true);
                    $('.image-search-container').prepend(button);

                    let loadMore = $('<button>').addClass('button button-primary').text('Load More');
                    loadMore.attr('type', 'button');
                    loadMore.attr('id', 'load-more');
                    if (totalPages > 1) {
                        loadMore.attr('disabled', false);
                    } else {
                        loadMore.attr('disabled', true);
                    }
                    $('.image-search-container').append(loadMore);

                    imageContainer.empty(); // Clear previous search results
                    // add active class to imageContainer
                    imageContainer.addClass('active');

                    $.each(images, function (index, image) {
                        let imageUrl = image.urls.small;
                        let imageElement = $('<img>').attr('src', imageUrl);
                        let checkbox = $('<input>').attr({
                            'type': 'checkbox',
                            'class': 'image-checkbox'
                        });
                        let span = $('<span>').addClass('checkmark');
                        let name = image.description || image.alt_description;
                        imageElement.attr({
                            'id': image.id,
                            'data-full': image.urls.full,
                            'alt': image.alt_description,
                            'data-label': name
                        });
                        let imageWrapper = $('<label>').addClass('image-wrapper');
                        let height = image.height;
                        let width = image.width;
                        if (height > width) {
                            imageWrapper.addClass('portrait');
                        }
                        imageWrapper.append(checkbox);
                        imageWrapper.append(span);
                        imageWrapper.append(imageElement);
                        imageContainer.append(imageWrapper);

                        // Toggle checkbox on image click
                        imageWrapper.click(function () {
                            checkbox.prop('checked', !checkbox.prop('checked'));
                            jmGetAllSelectedImages();
                        });
                    });
                } else {
                    // remove active class to imageContainer
                    $('#image-results').removeClass('active');
                    $('#image-results').html('<p>No images found for your search query.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error(status + ': ' + error);
            }
        });
    });

    $(document).on('click', '#load-more', function () {
        if (currentPage < totalPages) {
            currentPage++;
            let searchQuery = $('#image_search').val();
            let apiKey = jmUnsplashApi;
            let perPage = jmUnsplashPerPay;
            let apiUrl = `https://api.unsplash.com/search/photos/?client_id=${apiKey}&query=${searchQuery}&per_page=${perPage}&page=${currentPage}`;

            $.ajax({
                url: apiUrl,
                type: 'GET',
                success: function (response) {
                    if (response.results.length > 0) {
                        let images = response.results;
                        let imageContainer = $('#image-results');

                        $.each(images, function (index, image) {
                            let imageUrl = image.urls.small;
                            let imageElement = $('<img>').attr('src', imageUrl);
                            let checkbox = $('<input>').attr({
                                'type': 'checkbox',
                                'class': 'image-checkbox'
                            });
                            let span = $('<span>').addClass('checkmark');
                            let name = image.description || image.alt_description;
                            imageElement.attr({
                                'id': image.id,
                                'data-full': image.urls.full,
                                'alt': image.alt_description,
                                'data-label': name
                            });
                            let imageWrapper = $('<label>').addClass('image-wrapper');
                            let height = image.height;
                            let width = image.width;
                            if (height > width) {
                                imageWrapper.addClass('portrait');
                            }
                            imageWrapper.append(checkbox);
                            imageWrapper.append(span);
                            imageWrapper.append(imageElement);
                            imageContainer.append(imageWrapper);

                            // Toggle checkbox on image click
                            imageWrapper.click(function () {
                                checkbox.prop('checked', !checkbox.prop('checked'));
                                jmGetAllSelectedImages();
                            });
                        });
                    } else {
                        // Disable the 'Load More' button if no more results
                        $('#load-more').prop('disabled', true);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(status + ': ' + error);
                }
            });
        } else {
            // Disable the 'Load More' button if currentPage >= totalPages
            $('#load-more').prop('disabled', true);
        }
    });

    async function jmGetAllSelectedImages() {
        let selectedImages = [];
        let promises = [];

        $('.image-checkbox:checked').each(function () {
            let id = $(this).siblings('img').attr('id');
            let imageUrl = 'https://api.unsplash.com/photos/' + id + '?client_id=' + apiKey;

            let promise = new Promise(function(resolve, reject) {
                $.ajax({
                    url: imageUrl,
                    type: 'GET',
                    success: function (response) {
                        let links = response.urls;
                        let imageUrl = links.full;
                        let full_image_url = `${imageUrl}.jpg`;
                        let image = {
                            url: full_image_url,
                            alt: response.alt_description,
                            label: response.description,
                            id: response.id
                        };
                        if (!selectedImages.some(e => e.id === image.id)) {
                            selectedImages.push(image);
                        }
                        resolve(); // Resolve the promise once image details are collected
                    },
                    error: function (xhr, status, error) {
                        console.error(status + ': ' + error);
                        reject(error); // Reject the promise if there's an error
                    }
                });
            });

            promises.push(promise);
        });

        try {
            await Promise.all(promises); // Wait for all promises to resolve

            // Enable/disable button and update text based on selectedImages length
            if (selectedImages.length > 0) {
                $('#use-images').prop('disabled', false).text(`Add ${selectedImages.length} Selected Images to Media Library`);
            } else {
                $('#use-images').prop('disabled', true).text('No Images Selected');
            }
        } catch (error) {
            console.error('Error occurred: ' + error);
        }

        return selectedImages;
    }

    $(document).on('click', '#use-images', function () {
        // create a loading div
        let loading = $('<div>').addClass('loading');
        loading.html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
        // append the loading div to the image search container
        $('.image-search-container').append(loading);
        jmAddImagesToMediaLibrary();
    });

    async function jmAddImagesToMediaLibrary() {
        let selectedImages = await jmGetAllSelectedImages(); // Wait for selectedImages to resolve

        $.ajax({
            type: 'POST',
            url: jmUnsplashAjax.ajax_url,
            data: {
                action: 'jm_add_media',
                imageData: JSON.stringify(selectedImages)
            },
            success: function(response) {
                // remove every .image-wrapper
                $('.image-wrapper').remove();
                // remove button
                $('#use-images').remove();
                // remove button
                $('#load-more').remove();
                // remove search query
                $('#image_search').val('');
                // remove all checked checkboxes
                $('.image-checkbox:checked').prop('checked', false);
                // count how many images were added to media library
                let qty = response.data.length;
                let message;
                if(qty == 1) {
                    message = '<p>' + qty + ' images added to media library.</p>';
                } else if( qty > 1) {
                    message = '<p>' + qty + ' images added to media library.</p>';
                } else {
                    message = '<p>No images added to media library.</p>';
                }
                // display message
                $('.image-search-container').html(message);tempDiv.find('img').appendTo('#image-results');
            }
        });
    }

    // Disable form submit by default
    $('#image-delete-form').bind('submit',function(e){e.preventDefault();});

    // check valid entry to confrim deletion
    $('#image_delete').on('keyup', function () {
        let value = this.value;
        if (value == '42') {
            $('#deleteImagesbutton').prop('disabled', false);
            $('image-delete-form').bind('submit');
        } else {
            $('#deleteImagesbutton').prop('disabled', true);
            $('#image-delete-form').bind('submit',function(e){e.preventDefault();});
        }
    });

    // delete images
    $('#image-delete-form').on('submit', function (e) {
        e.preventDefault(); // prevent default form submit
        if($('#image_delete').val() == '42') {
            let loading = $('<div>').addClass('loading');
            loading.html('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>');
            // append the loading div to the image search container
            $('.image-search-container').append(loading);
            jmdeleeImages();
        }
    });

    async function jmdeleeImages() {
        let selectedImages = await jmGetAllSelectedImages(); // Wait for selectedImages to resolve

        $.ajax({
            type: 'POST',
            url: jmUnsplashAjax.ajax_url,
            data: {
                action: 'jm_delete_media',
            },
            success: function(response) {
            }
        });
    }

});