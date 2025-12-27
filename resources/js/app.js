import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);

// Inquiry modal component
window.inquiryModal = function(config) {
    return {
        open: false,
        loading: false,
        sent: false,
        error: '',
        to: config.to || '/inquiry',
        product: config.product || null,
        form: {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            message: ''
        },
        init() {
            window.addEventListener('open-inquiry-modal', (e) => {
                if (e.detail?.product) {
                    this.product = e.detail.product;
                }
                this.open = true;
            });
        },
        close() {
            this.open = false;
            this.error = '';
            if (this.sent) {
                this.form = { first_name: '', last_name: '', email: '', phone: '', message: '' };
                this.sent = false;
            }
        },
        async submit() {
            this.error = '';
            this.loading = true;

            try {
                const payload = {
                    ...this.form,
                    product_name: this.product?.name || null,
                    product_slug: this.product?.slug || null,
                    product_url: this.product?.url || null,
                };

                const response = await fetch(this.to, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.sent = true;
                } else {
                    this.error = data.message || 'Something went wrong. Please try again.';
                }
            } catch (e) {
                this.error = 'Network error. Please check your connection and try again.';
            } finally {
                this.loading = false;
            }
        }
    };
};

// Product carousel component for home page
window.productCarousel = function(totalItems) {
    return {
        currentIndex: 0,
        totalItems: totalItems,
        maxIndex: Math.max(0, totalItems - 3), // Show 3 at a time
        prev() {
            this.currentIndex = this.currentIndex > 0
                ? this.currentIndex - 1
                : this.maxIndex;
        },
        next() {
            this.currentIndex = this.currentIndex < this.maxIndex
                ? this.currentIndex + 1
                : 0;
        }
    };
};

window.Alpine = Alpine;
Alpine.start();
