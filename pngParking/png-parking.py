import qrcode
from PIL import Image

# Data untuk QR Code
data = {
    "kode": "GANPARKING",
}

# Ubah ke format string agar bisa di-scan dan di-decode
encoded = f"{data['kode']}"

# Buat QR Code
qr = qrcode.QRCode(
    version=1,
    error_correction=qrcode.constants.ERROR_CORRECT_H,
    box_size=10,
    border=4,
)
qr.add_data(encoded)
qr.make(fit=True)

# Generate gambar QR-nya
img = qr.make_image(fill_color="black", back_color="white")

# Simpan sebagai PNG
img.save(r"C:\laragon\www\pngParking\qrcode_parkir1.png")


# Buka untuk preview
img.show()
