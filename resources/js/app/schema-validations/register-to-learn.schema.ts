import { z } from 'zod';

export const registerSchema = z.object({
    first_name: z.string({
        message:'First name is required'
    }).min(2, 'First name is required'),
    last_name: z.string({
        message:'Last name is required'
    }).min(2, 'Last name is required'),
    phone: z.string({
        message:'Phone number is required'
    }).min(7, 'Phone is required'),
    email: z.string({
        message:'Email is required'
    }).email('Invalid email'),
    captcha: z.string({
        message:'Captcha is required'
    }).min(1, 'Captcha required'),
});

export type RegisterSchema = z.infer<typeof registerSchema>;
