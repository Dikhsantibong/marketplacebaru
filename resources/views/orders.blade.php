<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <!-- Tambahkan CSS Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #232946;
            --secondary: #EEBBC3;
            --thirty: #B8C1EC;
            --bg-main: #121629;
            --card: #FFFFFE;
        }

        .raised {
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.19), 0px 6px 6px rgba(0, 0, 0, 0.23);
        }

        html body {
            background-color: var(--bg-main) !important;
            display: flex;
            justify-content: center;
            overflow-x: hidden;
        }

        .image-product-detail {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cards {
            background-color: var(--primary);
            color: white;
            border-radius: 5px;
            border: 1px solid var(--thirty);
        }

        .image-product-detail img {
            width: 80%;
        }

        .buttonBuyNow button,
        .buttonAddCart button {
            background-color: var(--secondary);
            padding: 10px 20px;
            font-weight: bold;
            color: var(--primary);
        }

        .buttonExpression {
            display: flex;
            gap: 30px;
        }

        .circleRight,
        .circleLeft {
            position: absolute;
            width: 700px;
            aspect-ratio: 1/1;
            border-radius: 100%;
            filter: blur(180px);
            -webkit-filter: blur(180px);
            bottom: -5px;
            z-index: -1;
        }

        .circleRight {
            background-color: rgba(184, 193, 236, 0.63);
            left: -170px;
        }

        .circleLeft {
            background-color: rgba(238, 187, 195, 0.63);
            right: -170px;
        }
    </style>
</head>

<body>
    <div>
        <div class="circleRight"></div>
        <div class="circleLeft"></div>
        @include('navbar')
        <div class="pt-5"></div>
        <div class="pt-3"></div>
        <div class="container pt-5 ">
            <div class=" mb-3 raised" style="max-width: 940px; margin: auto;">
                <div class="row g-0 cards">
                    <div class="col-md-4 image-product-detail">
                        <img src="{{ asset($product->image_path) }}" class="img-fluid rounded-start"
                            alt="{{ $product->nama_product }}">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h2 class="card-title">{{ $product->nama_product }}</h2>
                            <p class="card-text lead">
                                Spesifikasi:
                                <br>
                                @php
                                    $specification = explode("\n", $product->spesifikasi);
                                @endphp

                                @foreach ($specification as $spec)
                                    {{ $spec }}<br>
                                @endforeach
                            </p>
                            <p class="card-text lead">Harga: {{ $product->harga }}</p>
                            <div class="buttonExpression">
                                <div class="buttonBuyNow">
                                    <button type="button" class="btn mt-3" data-toggle="modal" data-target="#paymentModal">Beli Sekarang</button>
                                </div>
                                <div class="buttonAddCart">
                                    <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn mt-3">Masukkan Keranjang</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content raised">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Pilih Metode Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('orders.store') }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <div class="form-group">
                                <label for="payment_method">Metode Pembayaran</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="BCA">BCA</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                </select>
                            </div>
                            <div id="payment-guide"></div>
                            <button type="submit" class="btn btn-primary mt-3">Konfirmasi Pembayaran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tambahkan JS Bootstrap dan jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#paymentModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var productId = button.data('product-id');
                var modal = $(this);
                modal.find('.modal-body input[name="product_id"]').val(productId);
            });

            $('#payment_method').change(function() {
                var selectedMethod = $(this).val();
                var harga = {{ $product->harga }};
                var totalHarga = harga;
                var guide = '';

                if (selectedMethod === 'BCA') {
                    guide =
                        '<div class="card bg-light mb-3"><div class="card-header text-center"><h5 class="card-title">Panduan Pembayaran BCA</h5></div><div class="card-body"><p>Transfer ke rekening BCA 1234567890 a/n AUDIO.</p><p>Total yang harus dibayar: <strong>Rp ' +
                        totalHarga + '</strong></p></div></div>';
                } else if (selectedMethod === 'BRI') {
                    guide =
                        '<div class="card bg-light mb-3"><div class="card-header text-center"><h5 class="card-title">Panduan Pembayaran BRI</h5></div><div class="card-body"><p>Transfer ke rekening BRI 9876543210 a/n AUDIO.</p><p>Total yang harus dibayar: <strong>Rp ' +
                        totalHarga + '</strong></p></div></div>';
                } else if (selectedMethod === 'BNI') {
                    guide =
                        '<div class="card bg-light mb-3"><div class="card-header text-center"><h5 class="card-title">Panduan Pembayaran BNI</h5></div><div class="card-body"><p>Transfer ke rekening BNI 98477767454 a/n AUDIO.</p><p>Total yang harus dibayar: <strong>Rp ' +
                        totalHarga + '</strong></p></div></div>';
                }

                $('#payment-guide').html(guide);
            });

            $('#addToCartForm').submit(function(event) {
                event.preventDefault();
                var form = $(this);
                var formData = {
                    product_id: $('input[name="product_id"]').val(),
                    quantity: 1,
                    _token: $('input[name="_token"]').val()
                };
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: formData,
                    success: function(response) {
                        alert('Produk berhasil ditambahkan ke keranjang');
                        var cartCount = parseInt($('#cart-count').text()) || 0;
                        $('#cart-count').text(cartCount + 1);
                    }
                });
            });

            $('#paymentForm').submit(function(event) {
                event.preventDefault();
                var form = $(this);
                var formData = {
                    product_id: $('input[name="product_id"]').val(),
                    quantity: 1,
                    payment_method: $('#payment_method').val(),
                    _token: $('input[name="_token"]').val()
                };
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect; // Redirect jika sukses
                        } else {
                            alert(response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error confirming payment. Please try again.');
                        console.error('Error confirming payment:', xhr.responseText);
                    }
                });
            });
        });
    </script>

    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="mr-auto">Keranjang</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            Produk berhasil ditambahkan ke keranjang.
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('form').submit(function(event) {
                event.preventDefault();
                var form = $(this);
                var formData = {
                    product_id: $('input[name="product_id"]').val(),
                    quantity: 1,
                    payment_method: $('#payment_method').val(),
                    _token: $('input[name="_token"]').val()
                };
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: formData,
                    success: function(response) {
                        var cartCount = parseInt($('#cart-count').text()) || 0;
                        $('#cart-count').text(cartCount + 1);
                    }
                });
            });
        });
    </script>
