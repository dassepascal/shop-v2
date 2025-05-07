<x-mail::message>
# Nouvelle de mande de contact

Une nouvelle demande de contact a été envoyé.

- Prénom : {{ $data['firstname'] }}
- Nom : {{ $data['name'] }}
- Email : {{ $data['email'] }}


**Message :**
{{ $data['subject'] }}

</x-mail::message>
