### CÀI ĐẶT VIETBOT TỪ ĐẦU TIÊN

### STEP1. Kết nối Console

1.1. Chờ Pi boot up xong, xác định IP của Pi từ Modem, Access Pint hoặc các phần mềm quét IP có hiển thị hostname

1.2. Sử dụng các phần mêm SSH như putty/Securec CRT truy cập ssh vào địa chỉ IP của Pi với 

```sh
username: pi
password: vietbot
```
### STEP2. Cài đặt môi trường

2.1. Nâng cấp gói
Trên console của Pi, sử dụng lần lượt các lệnh sau

```sh
sudo apt-get update
```
Sau khi chạy xong, chạy tiếp
```sh
sudo apt-get upgrade -y
```
2.2. Cài gói cơ bản
```sh
sudo apt-get install nano git -y
```
2.3. Cài đặt Swap 2G

```sh
sudo dphys-swapfile swapoff
```

```sh
sudo nano /etc/dphys-swapfile
```
Cửa sổ nano mở ra
Tại dòng 
```sh
CONF_SWAPSIZE=512
```
tăng giá trị 512 thành 2048, sau đó bấm Ctrl + Alt + X để save lại

2.4. Cài đặt Soundcard cho Mic2Hat, mạch AIO (Nếu sử dụng)
```sh
git clone https://github.com/waveshareteam/WM8960-Audio-HAT.git
```
Sau khi git xong
```sh
cd WM8960-Audio-HAT
sudo ./install.sh 
```
Sau khi cài xong
```sh
sudo reboot
```
2.5. Mở SPI cho led ws_2812 (Nếu sử dụng)
```sh
sudo raspi-config
```
Tìm đến mục Interface và kích hoạt mở SPI

### STEP3. Cài đặt các gói liên quan
3.1. Cài các gói phục vụ cho Python

```sh
sudo apt-get install libopenblas-dev portaudio19-dev vlc flac -y
```
và
```sh
sudo apt-get install python3 python3-pip python3-venv python3-dev python3-rpi.gpio python3-pyaudio
```

3.2. Tạo env
```sh
python3 -m venv vietbot_env
```
3.3. Chạy Env
```sh
source vietbot_env/bin/activate
```
Nếu ra dấu nhắc lệnh như sau:
```sh
(vietbot_env) pi@vietbot32:~ 
```
là thành công

3.4. Cài đặt gói Python
Trong môi trường evn, gõ
```sh
pip install pvporcupine python-vlc requests aiofiles aiohttp pyusb edge_tts sounddevice pyalsaaudio spidev SpeechRecognition pathlib2 gpiozero google-cloud google-cloud-speech google-cloud-texttospeech rpi_ws281x gTTS fuzzywuzzy websocket-client Quart python-Levenshtein pigpio RPi.GPIO lgpio numpy pvrecorder
```
### STEP4. Cài đặt & Chạy vietbot

4.1. Download code vietbot từ github
```sh
git clone --depth 1 https://github.com/phanmemkhoinghiep/vietbot_offline.git
```
Chờ cho đến khi kết thúc

4.2. Config vietbot

4.2.1. Lấy Mic id của microphone
Sử dụng lệnh 

```sh
python3 get_mic_id.py
```
Xem kết quả trả về, chọn lấy thiết bị Mic phù hợp
Ví dụ kết quả trả về
Danh sách các thiết bị âm thanh khả dụng:
```sh
ID: 0, Tên: bcm2835 Headphones: - (hw:0,0), Loại: 0 kênh đầu vào
ID: 1, Tên: USB Composite Device: Audio (hw:3,0), Loại: 1 kênh đầu vào
ID: 2, Tên: sysdefault, Loại: 0 kênh đầu vào
ID: 3, Tên: default, Loại: 0 kênh đầu vào
ID: 4, Tên: dmix, Loại: 0 kênh đầu vào
```
Thì mic_id sẽ là 1

4.2.2. Lấy Rate của microphone

Sử dụng lệnh với "hw: 3,0" lấy từ kết quả thực tế có được ở mục 4.2.1

```sh
arecord -D hw:3,0 --dump-hw-params
```
Ví dụ kết quả trả về:
```sh
Warning: Some sources (like microphones) may produce inaudible results
         with 8-bit sampling. Use '-f' argument to increase resolution
         e.g. '-f S16_LE'.
Recording WAVE 'stdin' : Unsigned 8 bit, Rate 8000 Hz, Mono
HW Params of device "hw:3,0":
--------------------
ACCESS:  MMAP_INTERLEAVED RW_INTERLEAVED
FORMAT:  S16_LE
SUBFORMAT:  STD
SAMPLE_BITS: 16
FRAME_BITS: 16
CHANNELS: 1
RATE: 32000
PERIOD_TIME: [1000 1000000]
PERIOD_SIZE: [32 32000]
PERIOD_BYTES: [64 64000]
PERIODS: [2 1024]
BUFFER_TIME: [2000 2000000]
BUFFER_SIZE: [64 64000]
BUFFER_BYTES: [128 128000]
TICK_TIME: ALL
--------------------
arecord: set_params:1352: Sample format non available
Available formats:
- S16_LE
```
Thì microphone này chỉ có 1 giá trị rate duy nhất là 32000

4.2.3. Lấy giá trị amixer_id (Giá trị id của soundcard)

```sh
amixer
```
Nếu kết quả trả về:

```sh
Simple mixer control 'PCM',0
  Capabilities: pvolume pvolume-joined pswitch pswitch-joined
  Playback channels: Mono
  Limits: Playback -10239 - 400
  Mono: Playback -3856 [60%] [-38.56dB] [on]
```
Thì amixer có giá trị là 0

4.2.4. Config picovoice
Mở file config.json they key của Picovoice ở dòng thứ 58
```sh
"key": "nA2Kkj/oRFQ=="
```
thành giá trị đã đăng ký trên Picovoice console


4.3. Chạy vietbot
Gõ các lệnh sau
```sh
cd /home/pi/vietbot_offline/raspberry/32/start.py
```
hoặc
```sh
cd /home/pi/vietbot_offline/raspberry/64/start.py
```
Tùy phiên bản OS
