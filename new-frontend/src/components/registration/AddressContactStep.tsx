import React from 'react';
import { UseFormRegister, FieldErrors } from 'react-hook-form';
import { ApplicantRegistrationData } from '@/lib/registration';

interface AddressContactStepProps {
  register: UseFormRegister<ApplicantRegistrationData>;
  errors: FieldErrors<ApplicantRegistrationData>;
}

export default function AddressContactStep({ register, errors }: AddressContactStepProps) {
  return (
    <div className="space-y-6">
      <h3 className="text-xl font-semibold text-gray-900 mb-6">Alamat & Informasi Kontak</h3>
      
      <div className="space-y-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Alamat *
          </label>
          <textarea
            {...register('address', { required: 'Alamat wajib diisi' })}
            rows={3}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Jl. Senayan No. 123"
          />
          {errors.address && (
            <p className="mt-1 text-sm text-red-600">{errors.address.message}</p>
          )}
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Kota *
            </label>
            <input
              {...register('city', { required: 'Kota wajib diisi' })}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Jakarta Selatan"
            />
            {errors.city && (
              <p className="mt-1 text-sm text-red-600">{errors.city.message}</p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Provinsi *
            </label>
            <input
              {...register('province', { required: 'Provinsi wajib diisi' })}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="DKI Jakarta"
            />
            {errors.province && (
              <p className="mt-1 text-sm text-red-600">{errors.province.message}</p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Kode Pos
            </label>
            <input
              {...register('postal_code')}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="12110"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Nomor WhatsApp *
            </label>
            <input
              {...register('whatsapp_number', { required: 'Nomor WhatsApp wajib diisi' })}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="+6281234567890"
            />
            {errors.whatsapp_number && (
              <p className="mt-1 text-sm text-red-600">{errors.whatsapp_number.message}</p>
            )}
          </div>
        </div>

        <div className="border-t pt-6">
          <h4 className="text-lg font-medium text-gray-900 mb-4">Kontak Darurat</h4>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Nama Kontak Darurat
              </label>
              <input
                {...register('emergency_contact_name')}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Jane Doe"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Telepon Kontak Darurat
              </label>
              <input
                {...register('emergency_contact_phone')}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="+6289876543210"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Hubungan
              </label>
              <input
                {...register('emergency_contact_relation')}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Saudara"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
