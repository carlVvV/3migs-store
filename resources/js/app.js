import './bootstrap';

import Alpine from 'alpinejs';
import { createVeriffFrame, MESSAGES as VeriffMessages } from '@veriff/incontext-sdk';

window.Alpine = Alpine;
window.createVeriffFrame = createVeriffFrame;
window.VeriffMessages = VeriffMessages;

Alpine.start();
