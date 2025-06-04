const {
  default: makeWASocket,
  DisconnectReason,
  useMultiFileAuthState,
  MessageType,
  MessageOptions,
  Mimetype,
} = require('@whiskeysockets/baileys');
const { Boom } = require('@hapi/boom');
const qrcode = require('qrcode');
const fs = require('fs');
const path = require('path');
const logger = require('../utils/logger');

class WhatsAppService {
  constructor() {
    this.sessions = new Map();
    this.sessionDir = path.join(__dirname, '../../sessions');
    this.defaultSession = 'main_session';
    
    // Ensure sessions directory exists
    if (!fs.existsSync(this.sessionDir)) {
      fs.mkdirSync(this.sessionDir, { recursive: true });
    }
  }

  async initialize() {
    logger.info('Initializing WhatsApp Service...');
    
    try {
      // Initialize default session
      await this.createSession(this.defaultSession);
      logger.info('WhatsApp Service initialized successfully');
    } catch (error) {
      logger.error('Failed to initialize WhatsApp Service:', error);
      throw error;
    }
  }

  async createSession(sessionId) {
    if (this.sessions.has(sessionId)) {
      logger.warn(`Session ${sessionId} already exists`);
      return this.sessions.get(sessionId);
    }

    logger.info(`Creating WhatsApp session: ${sessionId}`);
    
    const sessionPath = path.join(this.sessionDir, sessionId);
    if (!fs.existsSync(sessionPath)) {
      fs.mkdirSync(sessionPath, { recursive: true });
    }

    const { state, saveCreds } = await useMultiFileAuthState(sessionPath);
    
    const socket = makeWASocket({
      auth: state,
      printQRInTerminal: true,
      logger: {
        level: 'warn',
        print: (level, ...args) => {
          if (level === 'error' || level === 'warn') {
            logger.warn(`Baileys ${level}:`, ...args);
          }
        }
      },
      browser: ['Job Placement System', 'Desktop', '1.0.0'],
      generateHighQualityLinkPreview: true,
    });

    const sessionData = {
      socket,
      sessionId,
      status: 'connecting',
      qrCode: null,
      user: null,
      lastSeen: new Date(),
    };

    // Event handlers
    socket.ev.on('creds.update', saveCreds);
    
    socket.ev.on('connection.update', async (update) => {
      const { connection, lastDisconnect, qr } = update;
      
      if (qr) {
        try {
          sessionData.qrCode = await qrcode.toDataURL(qr);
          sessionData.status = 'qr_ready';
          logger.info(`QR Code generated for session ${sessionId}`);
        } catch (error) {
          logger.error('Failed to generate QR code:', error);
        }
      }
      
      if (connection === 'close') {
        const shouldReconnect = (lastDisconnect?.error as Boom)?.output?.statusCode !== DisconnectReason.loggedOut;
        
        if (shouldReconnect) {
          logger.info(`Session ${sessionId} disconnected, reconnecting...`);
          setTimeout(() => this.createSession(sessionId), 5000);
        } else {
          logger.info(`Session ${sessionId} logged out`);
          sessionData.status = 'logged_out';
        }
        
        this.sessions.delete(sessionId);
      } else if (connection === 'open') {
        sessionData.status = 'connected';
        sessionData.user = socket.user;
        sessionData.qrCode = null;
        logger.info(`Session ${sessionId} connected as ${socket.user?.name || socket.user?.id}`);
      }
    });

    socket.ev.on('messages.upsert', async (messageUpdate) => {
      try {
        await this.handleIncomingMessage(sessionId, messageUpdate);
      } catch (error) {
        logger.error('Error handling incoming message:', error);
      }
    });

    socket.ev.on('message-receipt.update', (updates) => {
      for (const update of updates) {
        this.updateMessageStatus(sessionId, update);
      }
    });

    this.sessions.set(sessionId, sessionData);
    return sessionData;
  }

  async sendMessage(sessionId, phoneNumber, message, options = {}) {
    const session = this.sessions.get(sessionId);
    
    if (!session || session.status !== 'connected') {
      throw new Error(`Session ${sessionId} is not connected`);
    }

    try {
      // Format phone number
      const jid = this.formatPhoneNumber(phoneNumber);
      
      let messageContent;
      
      if (options.type === 'image' && options.media) {
        messageContent = {
          image: options.media,
          caption: message || options.caption,
        };
      } else if (options.type === 'document' && options.media) {
        messageContent = {
          document: options.media,
          fileName: options.fileName || 'document',
          caption: message || options.caption,
        };
      } else {
        messageContent = { text: message };
      }

      const result = await session.socket.sendMessage(jid, messageContent);
      
      logger.info(`Message sent to ${phoneNumber} via session ${sessionId}`);
      
      return {
        success: true,
        messageId: result.key.id,
        timestamp: new Date(),
        recipient: phoneNumber,
        content: message,
      };
      
    } catch (error) {
      logger.error(`Failed to send message to ${phoneNumber}:`, error);
      throw error;
    }
  }

  async sendBulkMessages(sessionId, messages) {
    const session = this.sessions.get(sessionId);
    
    if (!session || session.status !== 'connected') {
      throw new Error(`Session ${sessionId} is not connected`);
    }

    const results = [];
    const delay = parseInt(process.env.MESSAGE_DELAY || '2000'); // 2 seconds delay between messages
    
    for (const msg of messages) {
      try {
        const result = await this.sendMessage(sessionId, msg.phoneNumber, msg.message, msg.options);
        results.push({ ...result, originalIndex: msg.index });
        
        // Add delay to avoid rate limiting
        if (delay > 0) {
          await new Promise(resolve => setTimeout(resolve, delay));
        }
        
      } catch (error) {
        logger.error(`Failed to send bulk message to ${msg.phoneNumber}:`, error);
        results.push({
          success: false,
          error: error.message,
          recipient: msg.phoneNumber,
          originalIndex: msg.index,
        });
      }
    }
    
    return results;
  }

  async getSessionInfo(sessionId) {
    const session = this.sessions.get(sessionId);
    
    if (!session) {
      return null;
    }
    
    return {
      sessionId: session.sessionId,
      status: session.status,
      user: session.user,
      qrCode: session.qrCode,
      lastSeen: session.lastSeen,
      connectedAt: session.connectedAt,
    };
  }

  async getAllSessions() {
    const sessions = [];
    
    for (const [sessionId, session] of this.sessions) {
      sessions.push({
        sessionId,
        status: session.status,
        user: session.user,
        lastSeen: session.lastSeen,
        connectedAt: session.connectedAt,
      });
    }
    
    return sessions;
  }

  async deleteSession(sessionId) {
    const session = this.sessions.get(sessionId);
    
    if (session) {
      try {
        await session.socket.logout();
      } catch (error) {
        logger.warn(`Error logging out session ${sessionId}:`, error);
      }
      
      this.sessions.delete(sessionId);
      
      // Delete session files
      const sessionPath = path.join(this.sessionDir, sessionId);
      if (fs.existsSync(sessionPath)) {
        fs.rmSync(sessionPath, { recursive: true, force: true });
      }
      
      logger.info(`Session ${sessionId} deleted`);
      return true;
    }
    
    return false;
  }

  async restartSession(sessionId) {
    await this.deleteSession(sessionId);
    return await this.createSession(sessionId);
  }

  formatPhoneNumber(phoneNumber) {
    // Remove all non-numeric characters
    let cleaned = phoneNumber.replace(/\D/g, '');
    
    // Add country code if not present (assuming Indonesia +62)
    if (cleaned.startsWith('0')) {
      cleaned = '62' + cleaned.substring(1);
    } else if (!cleaned.startsWith('62')) {
      cleaned = '62' + cleaned;
    }
    
    return cleaned + '@s.whatsapp.net';
  }

  async handleIncomingMessage(sessionId, messageUpdate) {
    const { messages, type } = messageUpdate;
    
    if (type !== 'notify') return;
    
    for (const message of messages) {
      if (message.key.fromMe) continue; // Skip messages sent by us
      
      const phoneNumber = message.key.remoteJid?.replace('@s.whatsapp.net', '');
      const messageText = message.message?.conversation || 
                         message.message?.extendedTextMessage?.text || '';
      
      logger.info(`Received message from ${phoneNumber}: ${messageText}`);
      
      // Here you can add logic to handle incoming messages
      // For example, auto-reply, webhook to main system, etc.
      
      try {
        // Send webhook to main system (optional)
        await this.sendWebhook({
          sessionId,
          phoneNumber,
          message: messageText,
          messageId: message.key.id,
          timestamp: new Date(message.messageTimestamp * 1000),
        });
      } catch (error) {
        logger.error('Failed to send webhook:', error);
      }
    }
  }

  async sendWebhook(data) {
    // Send incoming message data to main system
    const webhookUrl = process.env.WEBHOOK_URL;
    
    if (!webhookUrl) {
      return;
    }
    
    try {
      const axios = require('axios');
      await axios.post(webhookUrl, {
        type: 'incoming_message',
        data,
      }, {
        timeout: 5000,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${process.env.WEBHOOK_TOKEN}`,
        },
      });
      
      logger.info('Webhook sent successfully');
    } catch (error) {
      logger.error('Failed to send webhook:', error);
    }
  }

  updateMessageStatus(sessionId, update) {
    const { key, receipt } = update;
    
    logger.debug(`Message ${key.id} status updated to ${receipt.receiptTimestamp}`);
    
    // Here you can update message status in database
    // or send status update to main system
  }

  async cleanup() {
    logger.info('Cleaning up WhatsApp sessions...');
    
    for (const [sessionId, session] of this.sessions) {
      try {
        if (session.socket && session.status === 'connected') {
          await session.socket.end();
        }
      } catch (error) {
        logger.warn(`Error cleaning up session ${sessionId}:`, error);
      }
    }
    
    this.sessions.clear();
    logger.info('WhatsApp sessions cleaned up');
  }

  // Template message methods
  generateJobBroadcastMessage(applicantName, jobTitle, companyName, location, salaryRange) {
    return `🔔 *LOWONGAN KERJA BARU*

Halo ${applicantName}! 👋

Kami memiliki kesempatan kerja yang cocok untuk Anda:

🏢 *Perusahaan:* ${companyName}
💼 *Posisi:* ${jobTitle}
📍 *Lokasi:* ${location}
💰 *Gaji:* ${salaryRange}

Tertarik? Segera daftarkan diri Anda!

Untuk melamar, silakan hubungi tim HR kami atau kunjungi kantor kami.

Semoga beruntung! 🍀

_Pesan ini dikirim otomatis oleh sistem_`;
  }

  generateWelcomeMessage(applicantName, email, password) {
    return `🎉 *SELAMAT DATANG!* 🎉

Halo ${applicantName}!

Terima kasih telah mendaftar di sistem kami. Akun Anda telah berhasil dibuat dengan detail berikut:

📧 *Email:* ${email}
🔑 *Password:* ${password}

⚠️ *PENTING:* Harap segera ganti password Anda setelah login pertama kali untuk keamanan akun.

Kami akan menginformasikan lowongan pekerjaan yang sesuai dengan profil Anda melalui WhatsApp ini.

Semoga beruntung dalam pencarian kerja! 🍀

_Pesan otomatis dari sistem recruitment_`;
  }

  generateSelectionUpdateMessage(applicantName, jobTitle, companyName, stage, applicationNumber) {
    return `🎉 *UPDATE LAMARAN ANDA*

Halo ${applicantName}!

Kami dengan senang hati menginformasikan bahwa lamaran Anda untuk posisi *${jobTitle}* di ${companyName} telah berhasil maju ke tahap selanjutnya.

📋 *Tahap Saat Ini:* ${stage}
📞 *Nomor Aplikasi:* ${applicationNumber}

Pastikan nomor telepon Anda selalu aktif agar kami dapat menghubungi Anda.

Terima kasih atas kesabaran Anda. 🙏

_Pesan otomatis dari sistem recruitment_`;
  }
}

module.exports = WhatsAppService;