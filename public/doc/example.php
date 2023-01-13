<?php

namespace App\Http\Controllers; App\Http\Controllers;

use Illuminate\Http\Request;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use Illuminate\Support\Facades\Http;
use Exception; Exception;
use Session; Session;

class DocusignController extends Controller
{

    private $config, $args, $signer_client_id = 1000;
    /**
     * Show the html page
     *
     * @return render
     */
    public function index()
    {
        return view('docusign');return view('docusign');
    }

    /**
     * Connect your application to docusign
     *
     * @return url
     */
    public function connectDocusign()
    {
        try {
            $params = [
                'response_type' => 'code',
                'scope' => 'signature',
                'client_id' => env('DOCUSIGN_CLIENT_ID'),
                'state' => 'a39fh23hnf23',
                'redirect_uri' => route('docusign.callback'),
            ];
            $queryBuild = http_build_query($params);

            $url = "https://account-d.docusign.com/oauth/auth?";

            $botUrl = $url . $queryBuild;

            return redirect()->to($botUrl);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something Went wrong !');
        }
    }

    /**
     * This function called when you auth your application with docusign
     *
     * @return url
     */
    public function callback(Request $request)
    {
        $response = Http::withBasicAuth(env('DOCUSIGN_CLIENT_ID'), env('DOCUSIGN_CLIENT_SECRET'))
            ->post('https://account-d.docusign.com/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $request->code,
            ]);

        $result = $response->json();
        $request->session()->put('docusign_auth_code', $result['access_token']);

        return redirect()->route('docusign')->with('success', 'Docusign Successfully Connected');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function signDocument()
    {
        try{
            $this->args = $this->getTemplateArgs();
            $args = $this->args;

            $envelope_args = $args["envelope_args"];

            /* Create the envelope request object */
            $envelope_definition = $this->makeEnvelopeFileObject($args["envelope_args"]);
            $envelope_api = $this->getEnvelopeApi();

            $api_client = new \DocuSign\eSign\client\ApiClient($this->config);
            $envelope_api = new \DocuSign\eSign\Api\EnvelopesApi($api_client);
            $results = $envelope_api->createEnvelope($args['account_id'], $envelope_definition);
            $envelopeId = $results->getEnvelopeId();

            $authentication_method = 'None';
            $recipient_view_request = new \DocuSign\eSign\Model\RecipientViewRequest([
                'authentication_method' => $authentication_method,
                'client_user_id' => $envelope_args['signer_client_id'],
                'recipient_id' => '1',
                'return_url' => $envelope_args['ds_return_url'],
                'user_name' => 'savani', 'email' => 'savani@gmail.com'
            ]);

            $results = $envelope_api->createRecipientView($args['account_id'], $envelopeId, $recipient_view_request);

            return redirect()->to($results['url']);
        } catch (Exception $e) {
            dd($e->getMessage());
        }

    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    private function makeEnvelopeFileObject($args)
    {
        $docsFilePath = public_path('doc/demo_pdf_new.pdf');

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $contentBytes = file_get_contents($docsFilePath, false, stream_context_create($arrContextOptions));

        /* Create the document model */
        $document = new \DocuSign\eSign\Model\Document([
            'document_base64' => base64_encode($contentBytes),
            'name' => 'Example Document File',
            'file_extension' => 'pdf',
            'document_id' => 1
        ]);

        /* Create the signer recipient model */
        $signer = new \DocuSign\eSign\Model\Signer([
            'email' => 'savani@gmail.com',
            'name' => 'savani',
            'recipient_id' => '1',
            'routing_order' => '1',
            'client_user_id' => $args['signer_client_id']
        ]);

        /* Create a signHere tab (field on the document) */
        $signHere = new \DocuSign\eSign\Model\SignHere([
            'anchor_string' => '/sn1/',
            'anchor_units' => 'pixels',
            'anchor_y_offset' => '10',
            'anchor_x_offset' => '20'
        ]);

        /* Create a signHere 2 tab (field on the document) */
        $signHere2 = new \DocuSign\eSign\Model\SignHere([
            'anchor_string' => '/sn2/',
            'anchor_units' => 'pixels',
            'anchor_y_offset' => '40',
            'anchor_x_offset' => '40'
        ]);

        $signer->settabs(new \DocuSign\eSign\Model\Tabs(['sign_here_tabs' => [$signHere, $signHere2]]));

        $envelopeDefinition = new \DocuSign\eSign\Model\EnvelopeDefinition([
            'email_subject' => "Please sign this document sent from the ItSlutionStuff.com",
            'documents' => [$document],
            'recipients' => new \DocuSign\eSign\Model\Recipients(['signers' => [$signer]]),
            'status' => "sent",
        ]);

        return $envelopeDefinition;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function getEnvelopeApi(): EnvelopesApi
    {
        $this->config = new Configuration();
        $this->config->setHost($this->args['base_path']);
        $this->config->addDefaultHeader('Authorization', 'Bearer ' . $this->args['ds_access_token']);
        $this->apiClient = new ApiClient($this->config);

        return new EnvelopesApi($this->apiClient);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    private function getTemplateArgs()
    {
        $args = [
            'account_id' => env('DOCUSIGN_ACCOUNT_ID'),
            'base_path' => env('DOCUSIGN_BASE_URL'),
            'ds_access_token' => Session::get('docusign_auth_code'),
            'envelope_args' => [
                    'signer_client_id' => $this->signer_client_id,
                    'ds_return_url' => route('docusign')
                ]
        ];

        return $args;
    }

// 公式ドキュメントの記述
    public static function make_envelope(array $args, $clientService, $demoDocsPath): EnvelopeDefinition
        {
            # document 1 (html) has sign here anchor tag **signature_1**
            # document 2 (docx) has sign here anchor tag /sn1/
            # document 3 (pdf)  has sign here anchor tag /sn1/
            #
            # The envelope has two recipients.
            # recipient 1 - signer
            # recipient 2 - cc
            # The envelope will be sent first to the signer.
            # After it is signed, a copy is sent to the cc person.
            #
            # create the envelope definition
            $envelope_definition = new EnvelopeDefinition([
                'email_subject' => 'Please sign this document set'
            ]);
            $doc1_b64 = base64_encode($clientService->createDocumentForEnvelope($args));
            # read files 2 and 3 from a local directory
            # The reads could raise an exception if the file is not available!
            $content_bytes = file_get_contents($demoDocsPath . $GLOBALS['DS_CONFIG']['doc_docx']);
            $doc2_b64 = base64_encode($content_bytes);
            $content_bytes = file_get_contents($demoDocsPath . $GLOBALS['DS_CONFIG']['doc_pdf']);
            $doc3_b64 = base64_encode($content_bytes);

            # Create the document models
            $document1 = new Document([  # create the DocuSign document object
                'document_base64' => $doc1_b64,
                'name' => 'Order acknowledgement',  # can be different from actual file name
                'file_extension' => 'html',  # many different document types are accepted
                'document_id' => '1'  # a label used to reference the doc
            ]);
            $document2 = new Document([  # create the DocuSign document object
                'document_base64' => $doc2_b64,
                'name' => 'Battle Plan',  # can be different from actual file name
                'file_extension' => 'docx',  # many different document types are accepted
                'document_id' => '2'  # a label used to reference the doc
            ]);
            $document3 = new Document([  # create the DocuSign document object
                'document_base64' => $doc3_b64,
                'name' => 'Lorem Ipsum',  # can be different from actual file name
                'file_extension' => 'pdf',  # many different document types are accepted
                'document_id' => '3'  # a label used to reference the doc
            ]);
            # The order in the docs array determines the order in the envelope
            $envelope_definition->setDocuments([$document1, $document2, $document3]);

            # Create the signer recipient model
            $signer1 = new Signer([
                'email' => $args['signer_email'], 'name' => $args['signer_name'],
                'recipient_id' => "1", 'routing_order' => "1"]);
            # routingOrder (lower means earlier) determines the order of deliveries
            # to the recipients. Parallel routing order is supported by using the
            # same integer as the order for two or more recipients.

            # create a cc recipient to receive a copy of the documents
            $cc1 = new CarbonCopy([
                'email' => $args['cc_email'], 'name' => $args['cc_name'],
                'recipient_id' => "2", 'routing_order' => "2"]);

            return SMSDeliveryService::addSignersToTheDelivery($signer1, $cc1, $envelope_definition, $args);
        }
}