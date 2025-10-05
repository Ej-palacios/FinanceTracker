@extends('layouts.app')

@section('title', 'Contacto - FinanceTracker')

@section('content')

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Hero Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="bi bi-envelope-fill me-3"></i>Contáctanos
                </h1>
                <p class="lead text-muted fs-5">
                    Estamos aquí para ayudarte. ¡No dudes en ponerte en contacto con nosotros!
                </p>
            </div>

            <div class="row g-5">
                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header bg-gradient-primary text-white py-4">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="bi bi-chat-dots-fill me-2"></i>Envíanos un Mensaje
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="#" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label fw-semibold">Nombre *</label>
                                        <input type="text" class="form-control form-control-lg" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-semibold">Correo Electrónico *</label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label fw-semibold">Asunto *</label>
                                    <input type="text" class="form-control form-control-lg" id="subject" name="subject" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label fw-semibold">Mensaje *</label>
                                    <textarea class="form-control form-control-lg" id="message" name="message" rows="5" required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                        <i class="bi bi-send-fill me-2"></i>Enviar Mensaje
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-4">
                    <div class="row g-4">
                        <!-- Contact Information -->
                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-4 h-100">
                                <div class="card-header bg-success text-white py-4">
                                    <h5 class="card-title mb-0 fw-bold">
                                        <i class="bi bi-info-circle-fill me-2"></i>Información de Contacto
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-flex mb-3">
                                        <div class="contact-icon me-3">
                                            <i class="bi bi-envelope-fill text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Correo Electrónico</h6>
                                            <p class="text-muted mb-0">soporte@financetracker.com</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="contact-icon me-3">
                                            <i class="bi bi-telephone-fill text-success fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Teléfono</h6>
                                            <p class="text-muted mb-0">+505 1234-5678</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="contact-icon me-3">
                                            <i class="bi bi-clock-fill text-warning fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Horario de Atención</h6>
                                            <p class="text-muted mb-0">Lunes - Viernes: 8:00 AM - 6:00 PM</p>
                                            <p class="text-muted mb-0">Sábado: 9:00 AM - 2:00 PM</p>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="contact-icon me-3">
                                            <i class="bi bi-geo-alt-fill text-danger fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Ubicación</h6>
                                            <p class="text-muted mb-0">Managua, Nicaragua</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-4">
                                <div class="card-header bg-info text-white py-4">
                                    <h5 class="card-title mb-0 fw-bold">
                                        <i class="bi bi-share-fill me-2"></i>Síguenos
                                    </h5>
                                </div>
                                <div class="card-body p-4 text-center">
                                    <p class="text-muted mb-3">Conéctate con nosotros en redes sociales</p>
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="#" class="btn btn-outline-primary btn-lg rounded-circle">
                                            <i class="bi bi-facebook fs-5"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-info btn-lg rounded-circle">
                                            <i class="bi bi-twitter fs-5"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-lg rounded-circle">
                                            <i class="bi bi-instagram fs-5"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-success btn-lg rounded-circle">
                                            <i class="bi bi-whatsapp fs-5"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Preguntas Frecuentes</h2>
                        <p class="text-muted">Encuentra respuestas a las preguntas más comunes</p>
                    </div>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 shadow-sm rounded-3 mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    ¿Cómo puedo registrarme en FinanceTracker?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Para registrarte, haz clic en el botón "Registrarse" en la página principal y completa el formulario con tus datos personales.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm rounded-3 mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    ¿Es seguro usar FinanceTracker?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sí, utilizamos encriptación SSL y las mejores prácticas de seguridad para proteger tus datos financieros.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm rounded-3 mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    ¿Puedo acceder desde mi teléfono móvil?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sí, FinanceTracker es completamente responsive y funciona perfectamente en dispositivos móviles y tablets.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
