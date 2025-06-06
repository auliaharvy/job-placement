'use client';

import React, { useState } from 'react';
import { Agent } from '@/lib/agent';
import { AgentLinkGenerator } from '@/lib/agent-link-generator';

interface AgentLinkManagerProps {
  agent: Agent;
  baseUrl?: string;
}

export default function AgentLinkManager({ agent, baseUrl }: AgentLinkManagerProps) {
  const [copiedLink, setCopiedLink] = useState<string>('');
  const [showQRCode, setShowQRCode] = useState<boolean>(false);

  const agentId = agent.id.toString();
  const referralCode = agent.referral_code;

  // Generate all link variants
  const linkVariants = AgentLinkGenerator.generateAgentLinkVariants(agentId, referralCode, baseUrl);

  const copyToClipboard = async (text: string, label: string) => {
    try {
      await navigator.clipboard.writeText(text);
      setCopiedLink(label);
      setTimeout(() => setCopiedLink(''), 2000);
    } catch (error) {
      console.error('Failed to copy:', error);
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand('copy');
      document.body.removeChild(textArea);
      setCopiedLink(label);
      setTimeout(() => setCopiedLink(''), 2000);
    }
  };

  const openShareLink = (url: string) => {
    window.open(url, '_blank', 'width=600,height=400');
  };

  const qrCodeUrl = AgentLinkGenerator.generateQRCodeUrl(linkVariants.agentLink);

  return (
    <div className="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-lg font-semibold text-gray-900">
          Link Manager - {agent.user.full_name}
        </h3>
        <span className="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
          {agent.agent_code}
        </span>
      </div>

      {/* Agent Info */}
      <div className="bg-gray-50 rounded-md p-3 mb-4">
        <div className="grid grid-cols-2 gap-2 text-sm">
          <div><strong>Agent Code:</strong> {agent.agent_code}</div>
          <div><strong>Referral Code:</strong> {agent.referral_code}</div>
          <div><strong>Success Rate:</strong> {agent.success_rate}</div>
          <div><strong>Total Placements:</strong> {agent.successful_placements}</div>
        </div>
      </div>

      {/* Main Links */}
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Agent Link (ID-based)
          </label>
          <div className="flex gap-2">
            <input
              type="text"
              value={linkVariants.agentLink}
              readOnly
              className="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm font-mono"
            />
            <button
              onClick={() => copyToClipboard(linkVariants.agentLink, 'agent')}
              className={`px-4 py-2 rounded-md text-sm font-medium ${
                copiedLink === 'agent'
                  ? 'bg-green-600 text-white'
                  : 'bg-blue-600 text-white hover:bg-blue-700'
              }`}
            >
              {copiedLink === 'agent' ? '✓ Copied' : 'Copy'}
            </button>
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Referral Link (Code-based)
          </label>
          <div className="flex gap-2">
            <input
              type="text"
              value={linkVariants.referralLink}
              readOnly
              className="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm font-mono"
            />
            <button
              onClick={() => copyToClipboard(linkVariants.referralLink, 'referral')}
              className={`px-4 py-2 rounded-md text-sm font-medium ${
                copiedLink === 'referral'
                  ? 'bg-green-600 text-white'
                  : 'bg-blue-600 text-white hover:bg-blue-700'
              }`}
            >
              {copiedLink === 'referral' ? '✓ Copied' : 'Copy'}
            </button>
          </div>
        </div>
      </div>

      {/* UTM Tagged Links */}
      <div className="mt-6">
        <h4 className="text-md font-medium text-gray-900 mb-3">UTM Tagged Links</h4>
        <div className="space-y-3">
          {[
            { key: 'socialMediaLink', label: 'Social Media', source: 'social' },
            { key: 'emailLink', label: 'Email', source: 'email' },
            { key: 'whatsappLink', label: 'WhatsApp', source: 'whatsapp' }
          ].map(({ key, label, source }) => (
            <div key={key}>
              <label className="block text-sm font-medium text-gray-600 mb-1">
                {label} Link
              </label>
              <div className="flex gap-2">
                <input
                  type="text"
                  value={linkVariants[key as keyof typeof linkVariants]}
                  readOnly
                  className="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-xs font-mono"
                />
                <button
                  onClick={() => copyToClipboard(linkVariants[key as keyof typeof linkVariants], key)}
                  className={`px-3 py-2 rounded-md text-xs font-medium ${
                    copiedLink === key
                      ? 'bg-green-600 text-white'
                      : 'bg-gray-600 text-white hover:bg-gray-700'
                  }`}
                >
                  {copiedLink === key ? '✓' : 'Copy'}
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Share Buttons */}
      <div className="mt-6">
        <h4 className="text-md font-medium text-gray-900 mb-3">Quick Share</h4>
        <div className="flex flex-wrap gap-2">
          <button
            onClick={() => openShareLink(AgentLinkGenerator.generateWhatsAppShareLink(linkVariants.whatsappLink))}
            className="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 text-sm"
          >
            📱 WhatsApp
          </button>
          
          <button
            onClick={() => openShareLink(AgentLinkGenerator.generateTelegramShareLink(linkVariants.agentLink))}
            className="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm"
          >
            ✈️ Telegram
          </button>
          
          <button
            onClick={() => window.location.href = AgentLinkGenerator.generateEmailShareLink(linkVariants.emailLink)}
            className="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm"
          >
            📧 Email
          </button>
          
          <button
            onClick={() => setShowQRCode(!showQRCode)}
            className="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm"
          >
            📱 QR Code
          </button>
        </div>
      </div>

      {/* QR Code */}
      {showQRCode && (
        <div className="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
          <h4 className="text-md font-medium text-gray-900 mb-3">QR Code</h4>
          <div className="flex flex-col items-center space-y-3">
            <img
              src={qrCodeUrl}
              alt="QR Code for agent link"
              className="border border-gray-300 rounded-md"
              width={200}
              height={200}
            />
            <p className="text-sm text-gray-600 text-center">
              Scan this QR code to access the form with pre-selected agent
            </p>
            <div className="flex gap-2">
              <button
                onClick={() => copyToClipboard(qrCodeUrl, 'qr')}
                className="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
              >
                Copy QR URL
              </button>
              <a
                href={qrCodeUrl}
                download={`agent-${agent.agent_code}-qr.png`}
                className="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700"
              >
                Download QR
              </a>
            </div>
          </div>
        </div>
      )}

      {/* Usage Instructions */}
      <div className="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
        <h4 className="text-md font-medium text-blue-900 mb-2">How to Use</h4>
        <ul className="text-sm text-blue-800 space-y-1">
          <li>• <strong>Agent Link:</strong> Uses ?agent={agentId} parameter</li>
          <li>• <strong>Referral Link:</strong> Uses ?ref={referralCode} parameter</li>
          <li>• <strong>UTM Links:</strong> Include tracking parameters for analytics</li>
          <li>• <strong>Share Links:</strong> Pre-formatted for social platforms</li>
          <li>• <strong>QR Code:</strong> Perfect for offline sharing and print materials</li>
        </ul>
      </div>
    </div>
  );
}