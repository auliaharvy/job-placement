'use client';

import React, { useState } from 'react';
import AgentSelect from '@/components/ui/agent-select';
import { useAgentAutoFill } from '@/hooks/useAgentAutoFill';
import { Agent } from '@/lib/agent';

interface FormData {
  name: string;
  email: string;
  phone: string;
  agentId: string;
}

export default function ExampleForm() {
  const {
    selectedAgentId,
    selectedAgent,
    loading: agentLoading,
    error: agentError,
    setSelectedAgent,
    generateAgentLink
  } = useAgentAutoFill();

  const [formData, setFormData] = useState<FormData>({
    name: '',
    email: '',
    phone: '',
    agentId: selectedAgentId
  });

  const [generatedLink, setGeneratedLink] = useState<string>('');
  const [showLinkGenerator, setShowLinkGenerator] = useState(false);

  // Update form data when agent is auto-filled from URL
  React.useEffect(() => {
    if (selectedAgentId) {
      setFormData(prev => ({ ...prev, agentId: selectedAgentId }));
    }
  }, [selectedAgentId]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleAgentChange = (agentId: string, agent: Agent | null) => {
    setSelectedAgent(agentId, agent);
    setFormData(prev => ({
      ...prev,
      agentId: agentId
    }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    console.log('Form submitted:', formData);
    console.log('Selected agent:', selectedAgent);
    // Handle form submission here
  };

  const handleGenerateLink = () => {
    if (formData.agentId) {
      const link = generateAgentLink(formData.agentId);
      setGeneratedLink(link);
      setShowLinkGenerator(true);
    }
  };

  const copyToClipboard = () => {
    navigator.clipboard.writeText(generatedLink);
    alert('Link copied to clipboard!');
  };

  return (
    <div className="max-w-2xl mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">Example Form with Agent Selection</h1>
      
      {/* Display agent auto-fill status */}
      {agentLoading && (
        <div className="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
          <p className="text-blue-800">Loading agent information...</p>
        </div>
      )}
      
      {agentError && (
        <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
          <p className="text-red-800">{agentError}</p>
        </div>
      )}
      
      {selectedAgent && (
        <div className="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
          <p className="text-green-800">
            ✓ Agent auto-selected: <strong>{selectedAgent.user.full_name}</strong> ({selectedAgent.agent_code})
          </p>
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
            Name *
          </label>
          <input
            type="text"
            id="name"
            name="name"
            value={formData.name}
            onChange={handleInputChange}
            required
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <div>
          <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
            Email *
          </label>
          <input
            type="email"
            id="email"
            name="email"
            value={formData.email}
            onChange={handleInputChange}
            required
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <div>
          <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-1">
            Phone *
          </label>
          <input
            type="tel"
            id="phone"
            name="phone"
            value={formData.phone}
            onChange={handleInputChange}
            required
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <div>
          <label htmlFor="agent" className="block text-sm font-medium text-gray-700 mb-1">
            Agent *
          </label>
          <AgentSelect
            value={formData.agentId}
            onChange={handleAgentChange}
            placeholder="Select an agent"
          />
          {selectedAgent && (
            <div className="mt-2 text-sm text-gray-600">
              <p><strong>Agent:</strong> {selectedAgent.user.full_name}</p>
              <p><strong>Code:</strong> {selectedAgent.agent_code}</p>
              <p><strong>Referral Code:</strong> {selectedAgent.referral_code}</p>
              <p><strong>Success Rate:</strong> {selectedAgent.success_rate}</p>
            </div>
          )}
        </div>

        <div className="flex gap-4">
          <button
            type="submit"
            className="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            Submit
          </button>
          
          {formData.agentId && (
            <button
              type="button"
              onClick={handleGenerateLink}
              className="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            >
              Generate Link
            </button>
          )}
        </div>
      </form>

      {/* Link Generator */}
      {showLinkGenerator && (
        <div className="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
          <h3 className="text-lg font-semibold mb-2">Generated Agent Link</h3>
          <p className="text-sm text-gray-600 mb-2">
            Share this link to auto-select the agent in the form:
          </p>
          <div className="flex gap-2">
            <input
              type="text"
              value={generatedLink}
              readOnly
              className="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-white text-sm"
            />
            <button
              onClick={copyToClipboard}
              className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm"
            >
              Copy
            </button>
          </div>
          
          <div className="mt-3 text-sm text-gray-600">
            <p><strong>How to use:</strong></p>
            <ul className="list-disc list-inside mt-1 space-y-1">
              <li>Share this link with customers</li>
              <li>When they click the link, the agent will be automatically selected</li>
              <li>You can also use referral code: <code>?ref={selectedAgent?.referral_code}</code></li>
            </ul>
          </div>
        </div>
      )}
    </div>
  );
}